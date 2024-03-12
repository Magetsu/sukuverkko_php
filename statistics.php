<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");
require_once("html.php");
require_once("sql.php");

session_start();

/**
 *  Palataan takaisin pääsivulle.
 */
if(isset($_POST["logout_statistics"])) {
    
    LOGTEXT("Palataan <a href=\"main.php\">pääsivulle</a>.");
    
    if (!GET_LOGGING())
        
        redirect("main.php");
        
        exit;
}

create_html_start("Tilastotietoa");

create_html_header_panel();
create_html_statistics_upperbanner();
create_html_statistics_count_panel(get_individual_statistics_count());

if(isset($_POST["time_statistics"])) {
    
    $bdayarray = array();
    $ddayarray = array();
    
    for($year = 1700;$year<=date("Y");$year++) {
        
        array_push($bdayarray,get_count_by_year("bday",$year));
        array_push($ddayarray,get_count_by_year("dday",$year));

        //LOGTEXT("Vuonna $year syntyneitä oli : ".get_count_by_year("bday",$year)." kpl");
        //LOGTEXT("Vuonna $year kuolleita oli : ".get_count_by_year("dday",$year)." kpl");
        //LOGTEXT("------------------------------------------------------------------------------------------------------");
    }

    create_html_statistics_panel($bdayarray,$ddayarray);
}

create_html_end();

?>