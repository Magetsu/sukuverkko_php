<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");
require_once("sql.php");

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
            
            if (preg_match('/\n1 SEX (.+)/u', $gedcom_record, $match)) {
                
                $sex=trim($match[1]);
            } 
                           
            if (preg_match('/\n2 GIVN (.+)/u', $gedcom_record, $match)) {
                
                $givn=trim($match[1]);
            }  
            
            if (preg_match('/\n2 SURN (.+)/u', $gedcom_record, $match)) {
                
                $surn=trim($match[1]); 
            }
            
            if (preg_match('/\n1 OCCU (.+)/u', $gedcom_record, $match)) {
                
                $occu=trim($match[1]); 
            }
            
            if (preg_match_all('/\n1 SOUR @([A-Za-z0-9:_-]+)@*/', $gedcom_record, $sources, PREG_SET_ORDER)) {
                
                foreach($sources as $source) {
                    
                    $sourcedata .= trim($source[1])."¤";
                }
            }   
            
            if (preg_match_all('/\n1 (\w+).*(?:\n[2-9].*)*/', $gedcom_record, $matches, PREG_SET_ORDER)) {
                                
                foreach ($matches as $match) {
                    
                    if ( $match[1] == 'BIRT' ) {
                                               
                        $bday = modify_record_date($match[0]);
                        $bplace = modify_record_place($match[0]);
                        
                    } else if ( $match[1] == 'DEAT' ) {
                        
                        $isdead = '1';
                        
                        $dday = modify_record_date($match[0]);
                        $dplace = modify_record_place($match[0]);
                        
                       if (preg_match('/\n1 (?:\w+).*(?:\n[2-9].*)*(?:\n2 CAUS (.+))/u', $match[0], $dcausearray)) {
                            
                            list(,$dcause) = $dcausearray;
                            $dcause = trim($dcause);                        } 
                        
                    } else if ( $match[1] == 'BURI' ) {
                        
                        $buday = modify_record_date($match[0]);
                        $buplace = modify_record_place($match[0]);
                                                
                    } else if ( $match[1] == 'CHR' ) {

                        $chrday = modify_record_date($match[0]);
                        $chrplace = modify_record_place($match[0]);

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
                                                     
                        foreach($moves as $move) {
                            
                            $moveday = modify_move_date($move[2]);
                            $movedata .= $moveday." ".trim($move[3])."<br>";
                            }
                        }
                    }
                }
            }
           
            LOGTEXT("IMPORT_RECORD : xref    : ".$xref);
            LOGTEXT("IMPORT_RECORD : type    : ".$type);
            LOGTEXT("IMPORT_RECORD : Etunimi : ".$givn);
            LOGTEXT("IMPORT_RECORD : Sukunimi : ".$surn);
            LOGTEXT("IMPORT_RECORD : Sukupuoli : ".$sex);
            LOGTEXT("IMPORT_RECORD : Ammatti : ".$occu);
            LOGTEXT("IMPORT_RECORD : Syntymäpäivä : ".$bday);
            LOGTEXT("IMPORT_RECORD : Syntymäpaikka : ".$bplace);
            LOGTEXT("IMPORT_RECORD : Kuolinpäivä : ".$dday);
            LOGTEXT("IMPORT_RECORD : Kuolinpaikka : ".$dplace);
            LOGTEXT("IMPORT_RECORD : Kuolinsyy : ".$dcause);
            LOGTEXT("IMPORT_RECORD : Hautauspäivä : ".$buday);
            LOGTEXT("IMPORT_RECORD : Hautauspaikka : ".$buplace);
            LOGTEXT("IMPORT_RECORD : Kastepäivä : ".$chrday);
            LOGTEXT("IMPORT_RECORD : Kastepaikka : ".$chrplace);
            LOGTEXT("IMPORT_RECORD : Muutot : ".$movedata);
            LOGTEXT("IMPORT_RECORD : Huomio : ".$note);
            LOGTEXT("IMPORT_RECORD : Lähteet : ".$sourcedata);
            LOGTEXT("IMPORT_RECORD : Kuollut? : ".$isdead);

            LOGTEXT("INSERT INTO magetsu.familynet_individuals (xref, givn, surn, sex, occu, bday, bplace, dday, dplace, dcause, buday, buplace, chrday, chrplace, note, move, isdead, source) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            LOGTEXT("----------------------------------------------------------------------------------------------------------------------------------------------");

            $individual_record = array();
            array_push($individual_record, $xref, $givn, $surn, $sex, $occu, $bday, $bplace, $dday, $dplace, $dcause, $buday, $buplace, $chrday, $chrplace, $note, $movedata, $isdead, $sourcedata);
            import_individual_to_database($individual_record);
            
            break;
        case 'FAM':

            if (preg_match('/\n1 HUSB @([A-Za-z0-9:_-]+)@/', $gedcom_record, $match)) {
                
                $husb=trim($match[1]);
            } 
            
            if (preg_match('/\n1 WIFE @([A-Za-z0-9:_-]+)@/', $gedcom_record, $match)) {
                
                $wife=trim($match[1]);          
            } 
            
            if (preg_match_all('/\n1 CHIL @([A-Za-z0-9:_-]+)@*/', $gedcom_record, $children, PREG_SET_ORDER)) {
                
                foreach($children as $child) {
                    
                    $childdata .= trim($child[1])."¤";
                }
            }
            
            if (preg_match_all('/\n1 (\w+).*(?:\n[2-9].*)*/', $gedcom_record, $matches, PREG_SET_ORDER)) {
                
                foreach ($matches as $match) {
                    
                    if ( $match[1] == 'MARR' ) {
                        
                        $marday = modify_record_date($match[0]);
                        $marplace = modify_record_place($match[0]);        
                    }
                }
            }
            
            LOGTEXT("------------------------------------------------------------");
            LOGTEXT("IMPORT_RECORD : Syötetään FAM-tietue tietokantaan");
            LOGTEXT("IMPORT_RECORD : xref : ".$xref);
            LOGTEXT("IMPORT_RECORD : type : ".$type);
            LOGTEXT("IMPORT_RECORD : Mies : ".$husb);
            LOGTEXT("IMPORT_RECORD : Vaimo : ".$wife);
            LOGTEXT("IMPORT_RECORD : Hääpäivä : ".$marday);
            LOGTEXT("IMPORT_RECORD : Hääpaikka : ".$marplace);
            LOGTEXT("IMPORT_RECORD : Lapset : ".$childdata);
            
            LOGTEXT("INSERT INTO magetsu.familynet_families ($xref, $husb, $wife, $marday, $marplace, $childdata) values ( ?, ?, ?, ?, ?, ?)");       
            
            $family_record = array();
            array_push($family_record, $xref, $husb, $wife, $marday, $marplace, $childdata);
            import_family_to_database($family_record);
            
            break;
        case 'SOUR':
            
            $xref = trim($xref);
            
            if (preg_match('/\n1 TITL (.+)/u', $gedcom_record, $match)) {
                $name = trim($match[1]);
            }
            
            LOGTEXT("------------------------------------------------------------");
            LOGTEXT("IMPORT_RECORD : Syötetään SOUR-tietue tietokantaan");
            LOGTEXT("IMPORT_RECORD : xref : ".$xref);
            LOGTEXT("IMPORT_RECORD : type : ".$type);
            LOGTEXT("IMPORT_RECORD : kirja : ".$name);

            LOGTEXT("INSERT INTO magetsu.familynet_sources ($xref, $name) values ( ?, ?)");
 
            $source_record = array();
            array_push($source_record, $xref, $name);
            import_source_to_database($source_record);
            
            break;
    }    
}

function modify_record_date($datestring) {

    $date = '';
    
    LOGTEXT("MODIFY_RECORD_DATE : Muokataan päivämäärää : ".$datestring);
    if (preg_match('/\n1 (?:\w+).*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $datestring, $datearray)) {
               
        list(,$dateelements) = $datearray;
        
        if (preg_match('/^(\d*) ([A-Z]*) ([0-9]{4})/', $dateelements, $dateelementsarray)) {
                       
            list(,$day,$month,$year) = $dateelementsarray;
            
            $month = date('m',strtotime($month));
            $month = ltrim($month,'0');
            
            $date = $day.".".$month.".".$year;
            
        } else {
            
            $day = "";
            $month = "";
            
            $date = $dateelements;
        }
    }
    
    LOGTEXT("MODIFY_RECORD_DATE : Muokattu päivämäärä : ".$date);
    
    return $date;
}

function modify_move_date($datestring) {
        
    LOGTEXT("MODIFY_MOVE_DATE : Muokataan päivämäärää : ".$datestring);
    
    $date = '';
    
    if (preg_match('/^(\d*) ([A-Z]*) ([0-9]{4})/', $datestring, $dateelementsarray)) {
        
        list(,$day,$month,$year) = $dateelementsarray;
        
        $month = date('m',strtotime($month));
        
        $date = $day.".".$month.".".$year;        
        
    } else {
                
        $day = "";
        $month = "";
        
        $date = $datestring;
    }
    return $date;
}

function modify_record_place($placestring) {
    
    LOGTEXT("MODIFY_RECORD_PLACE : Muokataan paikkaa : ".$placestring);
    
    $place = '';
    
    if (preg_match('/\n1 (?:\w+).*(?:\n[2-9].*)*(?:\n2 PLAC (.+))/u', $placestring, $placearray)) {
        
        list(,$place) = $placearray;
        $place = trim($place);
    }
    
    return $place;
}

?>