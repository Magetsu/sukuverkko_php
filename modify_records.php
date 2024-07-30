<?php

/*
 * Muokataan päivämäärä muodosta "12 JAN 2021" muotoon "2021-01-12"
 */
function modify_record_day_to_date($day) {
    
    //LOGTEXT("MODIFY_RECORD_DAY_TO_DATE : Muokataan päivämäärää : ".$day);
    
    $date = '';
    $dayarray = '';
    $dayelementsarray = '';
    
    if (preg_match('/\n1 (?:\w+).*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $day, $dayarray)) {
        
        list(,$dayelements) = $dayarray;
        
        // Tuleva päiväys on muotoa 12 JAN 2021
        if (preg_match('/^(\d*) ([A-Z]*) ([0-9]{4})/', $dayelements, $dayelementsarray)) {
            
            list(,$day,$month,$year) = $dayelementsarray;
            
            $month = date('m',strtotime($month));
            $month = ltrim($month,'0');
            
            // Muokataan päiväys muotoon 2021-01-12
            $date = $year."-".$month."-".$day;            
            
        } else {
            
            // Päiväys on pelkkä vuosi
            $day = "0";
            $month = "0";
            
            $date = trim($dayelements)."-".$month."-".$day;
        }
    }
    
    //LOGTEXT("MODIFY_RECORD_DAY_TO_DATE : Muokattu päivämäärä : ".$date);
    
    return $date;
}

/*
 * Muokataan päivämäärä muodosta "2021-01-12" muotoon "12.1.2021"
 */
function modify_record_date_to_day($date) {
    
    //LOGTEXT("MODIFY_RECORD_DAY_TO_DATE : Muokataan päivämäärää : ".$date);
    
    $day = '';
    
    // Tuleva päiväys on muotoa 2021-01-12
    $date_elements_array = explode("-",$date);

    list($year,$month,$day) = $date_elements_array;

    // Poistetaan etunolla kuukausista ja päivistä
    $month = ltrim($month,'0');
    $day = ltrim($day,'0');
    
    // Jos vuosi on 0 niin palautetaan tyhjä
    if (ltrim($year,'0') == '') {
        
        return '';
    }

    // Jos ei ole päivää eikä kuukautta niin palautetaan pelkkä vuosi
    if (!$day && !$month) {
        
        return $year;
    }

    // Muokataan päiväys muotoon 12.1.2021
    
    $day = $day.".".$month.".".$year;
    
    //LOGTEXT("MODIFY_RECORD_DAY_TO_DATE : Muokattu päivämäärä : ".$day);
    
    return $day;
}

/*
 * Muokataan muuttotieto muodosta "1986 Paavola¤2000 Raahe¤" muotoon "1986 Paavola<br>2000 Raahe" tai "1986 Paavola. 2000 Raahe"
 */
function modify_move($mode, $move) {
    
    $line = '';
    
    $move_array = explode(DATA_DELIMITER,$move,-1);
    
    // Ei ole yhtään muuttoa joten palautetaan null
    if (!$move_array) {
        
        return null;
        
    } else {
        
        foreach ($move_array as $move) {
            
            switch ($mode) {
                case 'move_family_card':
                    
                    $line .= $move.". ";
                    break;
                    
                case 'move_individual':

                    $line .= $move."<br>";
                    break;          
            }
        }
        
        switch ($mode) {
            case 'move_family_card':
                $line = rtrim($line,'. ');
                break;
            case 'move_individual':
                // Poistetaan rivin viimeinen rivinvaihto
                $line = rtrim($line,'<br>');
                break;
        }
        return $line;
    }
    return null;
}

function modify_move_date($datestring) {
    
    LOGTEXT("MODIFY_MOVE_DATE : Muokataan päivämäärää : ".$datestring);

    $date_array = explode(" ",$datestring);
    
    LOGARRAY($date_array);
    
    if(count($date_array) > 1) {
        
        $month = date('m',strtotime($date_array[1]));
        $month = ltrim($month,'0');
        $date = $date_array[0].".".$month.".".$date_array[2];
        
    } else {
        
        $date = $date_array[0];
    }
    
    LOGTEXT("MODIFY_MOVE_DATE : Muokattu päivämäärä : ".$date);
    
    return $date;
}

function modify_record_place($placestring) {
    
    LOGTEXT("MODIFY_RECORD_PLACE : Muokataan paikkaa : ".$placestring);
    
    $place = '';
    $placearray = '';
    
    if (preg_match('/\n1 (?:\w+).*(?:\n[2-9].*)*(?:\n2 PLAC (.+))/u', $placestring, $placearray)) {
        
        list(,$place) = $placearray;
        $place = trim($place);
    }
    
    return $place;
}

?>