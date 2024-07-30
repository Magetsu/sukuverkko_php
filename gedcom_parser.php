<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");
require_once("sql.php");
require_once("modify_records.php");

function parse_file($gedcom) {
      
    LOGTEXT("PARSE_FILE : Käydään saatu tiedosto läpi.");

    $gedcom_data = fopen($gedcom, "r");
    $BLOCK_SIZE = 1024 * 4;
    $fcontents = "";
    
    while (!feof($gedcom_data)) {
        
        $temp = fread($gedcom_data, $BLOCK_SIZE);
        $fcontents .= $temp;
        $pos1 = 0;
            
        while ($pos1 !== false) {
            //-- find the start of the next record
        
            $pos2 = strpos($fcontents, "\n0", $pos1 +1);    // 
            
            while ((!$pos2) && (!feof($gedcom_data))) {
                
                $temp = fread($gedcom_data, $BLOCK_SIZE);
                $fcontents .= $temp;
                $pos2 = strpos($fcontents, "\n0", $pos1 +1);
            }
            
            //-- pull the next record out of the file
            if ($pos2) {
                
                $gedcom_record = substr($fcontents, $pos1, $pos2 - $pos1);
                
            } else {
                
                $gedcom_record = substr($fcontents, $pos1);
            }
            
            import_record_to_database($gedcom_record);
                                    
            $pos1 = $pos2;           
        }
        $fcontents = substr($fcontents, $pos2);
    }

    fclose($gedcom_data);  
}

function import_record_to_database($gedcom_record) {
  
    $xref = '';
    $type = '';
    $sex = '';
    $givn = '';
    $surn = '';
    $occu = '';
    $bday = '';
    $bplace = '';
    $dday = '';
    $dplace = '';
    $dcause = '';
    $buday = '';
    $buplace = '';
    $chrday = '';
    $chrplace = '';
    $note = '';
    $moves = '';
    $husb = '';
    $wife = '';
    $marday = '';
    $marplace = '';
    $isdead = '0';
    $movedata = '';
    $sourcedata = '';
    $childdata = '';
    $match = '';
    $sources = '';
    $matches = '';
    $dcausearray = '';
    $children = '';
    $famc = '';
    $famsdata = '';
    
    //LOGTEXT("   IMPORT_RECORD : Käydään läpi tietuetta : ".$gedcom_record);
    
    // import different types of records
    if (preg_match('/0 @([A-Za-z0-9:_-]+)@ ([_A-Z][_A-Z0-9]*)/', $gedcom_record, $match) > 0) {
        
        list(,$xref, $type) = $match;
                
    } elseif (preg_match('/0 ([_A-Z][_A-Z0-9]*)/', $gedcom_record, $match)) {
        
        $xref=$match[1];
        $type=$match[1];
    }
   
    switch ($type) {
        case 'INDI':

            LOGTEXT("-------------Importing record-----------------------------------------------------------------------------------------------------------------");
            LOGARRAY($gedcom_record);
            LOGTEXT("----------------------------------------------------------------------------------------------------------------------------------------------");
            LOGTEXT("IMPORT_RECORD : Syötetään INDI-tietue tietokantaan");
            
            // Etsitään tietueesta sukupuoli
            if (preg_match('/\n1 SEX (.+)/u', $gedcom_record, $match)) {
                
                $sex=trim($match[1]);
            } 
                           
            // Etsitään tietueesta etunimi
            if (preg_match('/\n2 GIVN (.+)/u', $gedcom_record, $match)) {
                
                $givn=trim($match[1]);
            }  
            
            // Etsitään tietueesta sukunimi
            if (preg_match('/\n2 SURN (.+)/u', $gedcom_record, $match)) {
                
                $surn=trim($match[1]); 
            }
            
            // Etsitään tietueesta ammatti
            if (preg_match('/\n1 OCCU (.+)/u', $gedcom_record, $match)) {
                
                $occu=trim($match[1]); 
            }
            
            // Etsitään tietueesta perhe jossa henkilö on lapsena
            if (preg_match('/\n1 FAMC @(.+)@/u', $gedcom_record, $match)) {
                
                $famc=trim($match[1]);
            }
            
            // Etsitään tietueesta perhe jossa henkilö on vanhempana
            if (preg_match_all('/\n1 FAMS @([A-Za-z0-9:_-]+)@*/u', $gedcom_record, $matches, PREG_SET_ORDER)) {
                
                LOGARRAY($matches);
                
                foreach($matches as $match) {
                    
                    $famsdata .= trim($match[1]).DATA_DELIMITER;
                }
            }
            
            // Etsitään tietueesta lähteet
            if (preg_match_all('/\n1 SOUR @([A-Za-z0-9:_-]+)@*/', $gedcom_record, $sources, PREG_SET_ORDER)) {
                
                foreach($sources as $source) {
                    
                    $sourcedata .= trim($source[1]).DATA_DELIMITER;
                }
            }   
            
            if (preg_match_all('/\n1 (\w+).*(?:\n[2-9].*)*/', $gedcom_record, $matches, PREG_SET_ORDER)) {
                                
                foreach ($matches as $match) {
                    
                    // Etsitään tietueesta syntymäpäivä
                    if ( $match[1] == 'BIRT' ) {
                                               
                        $bday = modify_record_day_to_date($match[0]);
                        $bplace = modify_record_place($match[0]);
                        
                    // Etsitään tietueesta kuolinpäivä
                    } else if ( $match[1] == 'DEAT' ) {
                        
                        $isdead = '1';
                        
                        $dday = modify_record_day_to_date($match[0]);
                        $dplace = modify_record_place($match[0]);
                        
                        // Etsitään tietueesta kuolinsyy
                        if (preg_match('/\n1 (?:\w+).*(?:\n[2-9].*)*(?:\n2 CAUS (.+))/u', $match[0], $dcausearray)) {
                            
                            list(,$dcause) = $dcausearray;
                            $dcause = trim($dcause);                        } 
                        
                    // Etsitään tietueesta hautauspäivä
                    } else if ( $match[1] == 'BURI' ) {
                        
                        $buday = modify_record_day_to_date($match[0]);
                        $buplace = modify_record_place($match[0]);
                                                
                    // Etsitään tietueesta ristimispäivä
                    } else if ( $match[1] == 'CHR' ) {

                        $chrday = modify_record_day_to_date($match[0]);
                        $chrplace = modify_record_place($match[0]);

                    // Etsitään tietueesta muistiinpanot
                    } else if ( $match[1] == 'NOTE' ) {
                                            
                        $contnotes = preg_split("/2 CONT/",$match[0]); 
                        $contnotes[0] = substr($contnotes[0], 8);     
                        
                        foreach($contnotes as $contnote) {
                            
                            $contnote = trim(strip_tags($contnote));
                            $note .= $contnote."<br>";
                            $note = trim($note);
                        }                        
                    } else if ( $match[1] == 'EVEN' ) {
                        
                        if(preg_match_all("/\n2 TYPE (\w+).*(?:\n2 DATE (.+))*(?:\n2 PLAC (.+))/", $match[0], $moves, PREG_SET_ORDER)) {
                                                     
                            LOGARRAY($moves);
                            
                            // Etsitään tietueesta muuttopäivät ja -paikat
                            foreach($moves as $move) {
                            
                            $moveday = modify_move_date($move[2]);
                            $movedata .= $moveday." ".trim($move[3]).DATA_DELIMITER;
                            }
                        }
                    }
                }
            }
           
            $individual_record = array();
            array_push($individual_record, $xref, $givn, $surn, $sex, $occu, $bday, $bplace, $dday, $dplace, $dcause, $buday, $buplace, $chrday, $chrplace, $note, $movedata, $isdead, $sourcedata, $famc, $famsdata);
            
            display_individual_card($individual_record);
            import_individual_to_database($individual_record);
            
            break;
        case 'FAM':

            // Etsitään tietueesta mies
            if (preg_match('/\n1 HUSB @([A-Za-z0-9:_-]+)@/', $gedcom_record, $match)) {
                
                $husb=trim($match[1]);
            } 
            
            // Etsitään tietueesta vaimo
            if (preg_match('/\n1 WIFE @([A-Za-z0-9:_-]+)@/', $gedcom_record, $match)) {
                
                $wife=trim($match[1]);          
            } 
            
            // Etsitään tietueesta lapset
            if (preg_match_all('/\n1 CHIL @([A-Za-z0-9:_-]+)@*/', $gedcom_record, $children, PREG_SET_ORDER)) {
                
                foreach($children as $child) {
                    
                    $childdata .= trim($child[1])."¤";
                }
            }
            
            // Etsitään tietueesta naimapäivä ja -paikka
            if (preg_match_all('/\n1 (\w+).*(?:\n[2-9].*)*/', $gedcom_record, $matches, PREG_SET_ORDER)) {
                
                foreach ($matches as $match) {
                    
                    if ( $match[1] == 'MARR' ) {
                        
                        $marday = modify_record_day_to_date($match[0]);
                        $marplace = modify_record_place($match[0]);        
                    }
                }
            }
                        
            $family_record = array();
            array_push($family_record, $xref, $husb, $wife, $marday, $marplace, $childdata);
            
            display_family_card($family_record);
            import_family_to_database($family_record);
            
            break;
        case 'SOUR':
            
            $xref = trim($xref);
            
            // Etsitään tietueesta lähteet
            if (preg_match('/\n1 TITL (.+)/u', $gedcom_record, $match)) {
                $name = trim($match[1]);
            }
             
            $source_record = array();
            array_push($source_record, $xref, $name);
            
            display_source_card($source_record);
            import_source_to_database($source_record);
            
            break;
    }    
}

function display_individual_card($individual_record) {

    LOGTEXT("IMPORT_RECORD : xref    : ".$individual_record[0]);
    LOGTEXT("IMPORT_RECORD : Etunimi : ".$individual_record[1]);
    LOGTEXT("IMPORT_RECORD : Sukunimi : ".$individual_record[2]);
    LOGTEXT("IMPORT_RECORD : Sukupuoli : ".$individual_record[3]);
    LOGTEXT("IMPORT_RECORD : Ammatti : ".$individual_record[4]);
    LOGTEXT("IMPORT_RECORD : Syntymäpäivä : ".$individual_record[5]);
    LOGTEXT("IMPORT_RECORD : Syntymäpaikka : ".$individual_record[6]);
    LOGTEXT("IMPORT_RECORD : Kuolinpäivä : ".$individual_record[7]);
    LOGTEXT("IMPORT_RECORD : Kuolinpaikka : ".$individual_record[8]);
    LOGTEXT("IMPORT_RECORD : Kuolinsyy : ".$individual_record[9]);
    LOGTEXT("IMPORT_RECORD : Hautauspäivä : ".$individual_record[10]);
    LOGTEXT("IMPORT_RECORD : Hautauspaikka : ".$individual_record[11]);
    LOGTEXT("IMPORT_RECORD : Kastepäivä : ".$individual_record[12]);
    LOGTEXT("IMPORT_RECORD : Kastepaikka : ".$individual_record[13]);
    LOGTEXT("IMPORT_RECORD : Huomio : ".$individual_record[14]);
    LOGTEXT("IMPORT_RECORD : Muutot : ".$individual_record[15]);
    LOGTEXT("IMPORT_RECORD : Kuollut? : ".$individual_record[16]);
    LOGTEXT("IMPORT_RECORD : Lähteet : ".$individual_record[17]);
    LOGTEXT("IMPORT_RECORD : Perhe lapsena : ".$individual_record[18]);
    LOGTEXT("IMPORT_RECORD : Perhe vanhempana : ".$individual_record[19]);
    
    LOGTEXT("INSERT INTO magetsu.familynet_individuals (xref, givn, surn, sex, occu, bday, bplace, dday, dplace, dcause, buday, buplace, chrday, chrplace, note, move, isdead, source, famc, fams )");
    LOGTEXT("----------------------------------------------------------------------------------------------------------------------------------------------");
}

function display_family_card($family_record) {
    
    LOGTEXT("------------------------------------------------------------");
    LOGTEXT("IMPORT_RECORD : Syötetään FAM-tietue tietokantaan");
    LOGTEXT("IMPORT_RECORD : xref : ".$family_record[0]);
    LOGTEXT("IMPORT_RECORD : Mies : ".$family_record[1]);
    LOGTEXT("IMPORT_RECORD : Vaimo : ".$family_record[2]);
    LOGTEXT("IMPORT_RECORD : Hääpäivä : ".$family_record[3]);
    LOGTEXT("IMPORT_RECORD : Hääpaikka : ".$family_record[4]);
    LOGTEXT("IMPORT_RECORD : Lapset : ".$family_record[5]);
    
    LOGTEXT("INSERT INTO magetsu.familynet_families (xref, husb, wife, marday, marplace, childdata) values ( ?, ?, ?, ?, ?, ?)");
}

function display_source_card($source_record) {
    
    LOGTEXT("------------------------------------------------------------");
    LOGTEXT("IMPORT_RECORD : Syötetään SOUR-tietue tietokantaan");
    LOGTEXT("IMPORT_RECORD : xref : ".$source_record[0]);
    LOGTEXT("IMPORT_RECORD : kirja : ".$source_record[1]);
    
    LOGTEXT("INSERT INTO magetsu.familynet_sources (xref, name) values ( ?, ?)");
}

?>