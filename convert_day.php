<?php

function modify_record_day_to_date($day) {
    
    //LOGTEXT("MODIFY_RECORD_DAY_TO_DATE : Muokataan päivämäärää : ".$day);
    
    $date = '';
    $dayarray = '';
    $dayelementsarray = '';
    
    if (preg_match('/\n1 (?:\w+).*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $day, $dayarray)) {
        
        list(,$dayelements) = $dayarray;
        
        // Tuleva päiväys on muotoa 12 NOV 2021
        if (preg_match('/^(\d*) ([A-Z]*) ([0-9]{4})/', $dayelements, $dayelementsarray)) {
            
            list(,$day,$month,$year) = $dayelementsarray;
            
            $month = date('m',strtotime($month));
            $month = ltrim($month,'0');
            
            // Muokataan päiväys muotoon 2021-11-12
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

function modify_record_date_to_day($date) {
    
    //LOGTEXT("MODIFY_RECORD_DAY_TO_DATE : Muokataan päivämäärää : ".$date);
    
    $day = '';
    
    // Tuleva päiväys on muotoa 2021-11-12
    if (preg_match('/^([0-9]{4})-([0-9]*)-(\d*)/', $date, $dateelementsarray)) {
        
        list(,$year,$month,$day) = $dateelementsarray;
       
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
            
        $day = $day.".".$month.".".$year;
        
    } 
        
    //LOGTEXT("MODIFY_RECORD_DAY_TO_DATE : Muokattu päivämäärä : ".$day);
    
    return $day;
}
        
?>