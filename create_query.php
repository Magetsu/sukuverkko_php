<?php

/**
 * Luodaan SQL-kysely saaduista rajaustiedoista
 *
 */
function get_constraint_query($constraint,$count) {
    
    LOGTEXT("Haetaan rajauksella nimi : ".$_POST[$constraint]);
    
    $subquery = '';
    
    if ($count > 0) {
        
        $subquery = " AND ";
    }
    
    switch ($constraint) {
        case 'givn':
        case 'surn':
        case 'dcause':
        case 'occu':
        case 'move':
        case 'husbgivn':
        case 'husbsurn':
        case 'wifegivn':
        case 'wifesurn':
            
            $subquery .= " $constraint LIKE '%".$_POST[$constraint]."%'";
            
            break;
            
        case 'bplace':
        case 'dplace':
        case 'buplace':
        case 'chrplace':
        case 'husbbplace':
        case 'husbdplace':
        case 'wifebplace':
        case 'wifedplace':
        case 'marplace':
            
            $subquery .= " $constraint LIKE '".$_POST[$constraint]."'";
            
            break;
    }
    
    $_SESSION[$constraint] = $_POST[$constraint];
    
    return $subquery;
}

/**
 * Luodaan SQL-lauseke aikarajoituksista
 *
 */
function create_time_search_query($element, $time_string) {
    
    LOGTEXT("CREATE_TIME_SEARCH_QUERY : Luodaan hakujen aikaleimat : ".$element." aikarajalla ".$time_string);
    
    $timearray = explode("-", $time_string);
    
    LOGARRAY($timearray);
    
    // Hakukriteerinä tulee tasan yksi aika
    if (sizeof($timearray) == 1) {
        
        $date = explode(".", $timearray[0]);
        
        LOGARRAY($date);
        
        // Aikana tulee pelkkä vuosiluku
        if (sizeof($date) == 1) {
            
            //SELECT * FROM `familynet_individuals_v2` WHERE YEAR(bday) = '1972';
            $time_query = " YEAR($element) = '".$date[0]."'";
            
            // Aikana tulee vuosi-kuukausi-päivä
        } else {
            
            // SELECT * FROM `familynet_individuals_v2` WHERE bday = '1972-01-01';
            $time_query = " $element = '".$date[2]."-".$date[1]."-".$date[0]."'";
        }
        
        // Hakukriteerinä tulee ennen tätä aikaa
    } else if ($timearray[1] && !$timearray[0]) {
        
        LOGTEXT("Haettu aika : -".$timearray[1]);
        
        $date = explode(".", $timearray[1]);
        
        LOGARRAY($date);
        
        // Aikana tulee pelkkä vuosiluku
        if (sizeof($date) == 1) {
            
            //SELECT * FROM `familynet_individuals_v2` WHERE YEAR(bday) < '1972';
            
            $time_query = " YEAR($element) <= '".$date[0]."'";
            
            // Aikana tulee päivä.kuukausi.vuosi
        } else {
            
            // SELECT * FROM `familynet_individuals_v2` WHERE bday < '1972-01-01';
            $time_query = " $element <= '".$date[2].".".$date[1].".".$date[0]."'";
        }
        
        // Hakukriteerinä tulee tämän ajan jälkeen
    } else if (!$timearray[1] && $timearray[0]) {
        
        LOGTEXT("Haettu aika : ".$timearray[0]."-");
        
        $date = explode(".", $timearray[0]);
        
        LOGARRAY($date);
        
        // Aikana tulee pelkkä vuosiluku
        if (sizeof($date) == 1) {
            
            //SELECT * FROM `familynet_individuals_v2` WHERE YEAR(bday) > '1972';
            
            $time_query = " YEAR($element) >= '".$date[0]."'";
            
            // Aikana tulee päivä.kuukausi.vuosi
        } else {
            
            // SELECT * FROM `familynet_individuals_v2` WHERE bday > '1972-01-01';
            $time_query = " $element >= '".$date[2].".".$date[1].".".$date[0]."'";
        }
        
        
        
        // Hakukriteerinä tulee näiden aikojen välissä
    } else if ($timearray[0] && $timearray[1]) {
        
        LOGTEXT("Haettu aika : ".$timearray[0]."-".$timearray[1]);
        
        $date_before = explode(".", $timearray[0]);
        
        $date_after = explode(".", $timearray[1]);
        
        LOGARRAY($date_before);
        LOGARRAY($date_after);
        
        // Aikana tulee pelkkä vuosiluku
        if ((sizeof($date_before) == 1) && (sizeof($date_after) == 1)) {
            
            //SELECT * FROM `familynet_individuals_v2` WHERE YEAR(bday) BETWEEN '1972' AND '1973';
            
            $time_query = " YEAR($element) BETWEEN '".$date_before[0]."' AND '".$date_after[0]."'";
            
            // Aikana tulee päivä.kuukausi.vuosi
        } else {
            
            // SELECT * FROM `familynet_individuals_v2` WHERE bday BETWEEN '2021-01-01' AND '2022-11-01';
            $time_query = " $element BETWEEN '".$date_before[2].".".$date_before[1].".".$date_before[0]."' AND '".$date_after[2].".".$date_after[1].".".$date_after[0]."'";
        }
    }
    
    return $time_query;
}

/**
 * Tyhjennetään hakukentät
 *
 */
function clear_individual_constraints_fields() {
    
    LOGTEXT("CLEAR_INDIVIDUAL_CONSTRAINTS_FIELDS : Tyhjennetään henkilön rajauskentät");
    
    $_SESSION["givn"] = "";
    $_SESSION["surn"] = "";
    $_SESSION["occu"] = "";
    $_SESSION["bday"] = "";
    $_SESSION["bplace"] = "";
    $_SESSION["dday"] = "";
    $_SESSION["dplace"] = "";
    $_SESSION["dcause"] = "";
    $_SESSION["buday"] = "";
    $_SESSION["buplace"] = "";
    $_SESSION["chrday"] = "";
    $_SESSION["chrplace"] = "";
    $_SESSION["move"] = "";
    
    $_SESSION["individual_constraints_search"] = false;
    $_SESSION["individual_name_search"] = '';
}

/**
 * Tyhjennetään hakukentät
 *
 */
function clear_marriage_constraints_fields() {
    
    LOGTEXT("CLEAR_MARRIAGE_CONSTRAINTS_FIELDS : Tyhjennetään avioliiton rajauskentät");
    
    $_SESSION["husbgivn"] = "";
    $_SESSION["husbsurn"] = "";
    $_SESSION["husbbday"] = "";
    $_SESSION["husbbplace"] = "";
    $_SESSION["husbdday"] = "";
    $_SESSION["husbdplace"] = "";
    $_SESSION["wifegivn"] = "";
    $_SESSION["wifesurn"] = "";
    $_SESSION["wifebday"] = "";
    $_SESSION["wifebplace"] = "";
    $_SESSION["wifedday"] = "";
    $_SESSION["wifedplace"] = "";
    $_SESSION["marday"] = "";
    $_SESSION["marplace"] = "";
}

?>