<?php

require_once("logging.php");
require_once("html.php");
require_once("sql.php");

session_start();

define ("RESULTS_IN_PAGE", 1000);

$query = null;
$pagenumber = 0;

if(isset($_GET["page"]) && ctype_digit($_GET["page"])) {
    
    $pagenumber = $_GET["page"];
        
    if ($_SESSION["individual_constraints_search"]) {
        
        $query = $_SESSION["individual_constraints_query"];
        
    } else if ($_SESSION["individual_name_search"])  {
        
        $query = $_SESSION["individual_name_query"];
    }
}

/*
 * Haetaan yleishaulla.
 */
if(isset($_POST["individual_search"])) {
    
    LOGTEXT("POST[individual_search]) : Haetaan tietokannasta nimellä ".$_POST["individual"]);
        
    clear_constraints_fields();
    
    $query = " givn LIKE '%".$_POST["individual"]."%'
            OR surn LIKE '%".$_POST["individual"]."%'
            OR occu LIKE '%".$_POST["individual"]."%'
            OR bday LIKE '%".$_POST["individual"]."%'
            OR bplace LIKE '%".$_POST["individual"]."%'
            OR dday LIKE '%".$_POST["individual"]."%'
            OR dplace LIKE '%".$_POST["individual"]."%'
            OR dcause LIKE '%".$_POST["individual"]."%'
            OR buday LIKE '%".$_POST["individual"]."%'
            OR buplace LIKE '%".$_POST["individual"]."%'
            OR chrday LIKE '%".$_POST["individual"]."%'
            OR chrplace LIKE '%".$_POST["individual"]."%'
            OR move LIKE '%".$_POST["individual"]."%'
            OR source LIKE '%".$_POST["individual"]."%'
            OR note LIKE '%".$_POST["individual"]."%'
            ";
       
    // Hakukentän tieto laitetaan talteen
    $_SESSION["individual_name"] = $_POST["individual"];
    
    // Haku laitetaan talteen
    $_SESSION["individual_name_query"] = $query;
    
    // On tehty yleinen haku
    $_SESSION["individual_name_search"] = true;

    LOGTEXT("_POST[individual]) : Hakulauseke on : ".$query."  :  individual_name_search = ".$_SESSION["individual_name_search"]);
}

/*
 * Haetaan rajoitushaulla.
 */
if(isset($_POST["individual_constraints_search"])) {
   
    $constraintsCount = 0;
    clear_constraints_fields();
    
    $_SESSION["individual_name"] = "";
    
    if ($_POST["givn"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["givn"]);
        
        $query .= " givn LIKE '%".$_POST["givn"]."%'";
        
        ++$constraintsCount;
        
        $_SESSION["givn"] = $_POST["givn"];
    }
    
    if ($_POST["surn"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["surn"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= " surn LIKE '".$_POST["surn"]."'";
        
        ++$constraintsCount;
        
        $_SESSION["surn"] = $_POST["surn"];
    }

    if ($_POST["bplace"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["bplace"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= " bplace LIKE '".$_POST["bplace"]."'";
        
        ++$constraintsCount;
        
        $_SESSION["bplace"] = $_POST["bplace"];
    }
    
    if ($_POST["dplace"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["dplace"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= " dplace LIKE '".$_POST["dplace"]."'";
        
        ++$constraintsCount;
        
        $_SESSION["dplace"] = $_POST["dplace"];
    }
    
    if ($_POST["dcause"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["dcause"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= " dcause LIKE '%".$_POST["dcause"]."%'";
        
        ++$constraintsCount;
        
        $_SESSION["dcause"] = $_POST["dcause"];
    }
    
    if ($_POST["buplace"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["buplace"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= " buplace LIKE '".$_POST["buplace"]."'";
        
        ++$constraintsCount;
        
        $_SESSION["buplace"] = $_POST["buplace"];
    }
    
    if ($_POST["chrplace"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["chrplace"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= " chrplace LIKE '".$_POST["chrplace"]."'";
        
        ++$constraintsCount;
        
        $_SESSION["chrplace"] = $_POST["chrplace"];
    }

    if ($_POST["occu"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["occu"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= " occu LIKE '%".$_POST["occu"]."%'";
        
        ++$constraintsCount;
        
        $_SESSION["occu"] = $_POST["occu"];
    }

    if ($_POST["bday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["bday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("bday", $_POST["bday"]);
        
        ++$constraintsCount;
        
        $_SESSION["bday"] = $_POST["bday"];
    }
    
    if ($_POST["buday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["buday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("buday", $_POST["buday"]);
        
        ++$constraintsCount;
        
        $_SESSION["buday"] = $_POST["buday"];
    }
    
    if ($_POST["dday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["dday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("dday", $_POST["dday"]);
        
        ++$constraintsCount;
        
        $_SESSION["dday"] = $_POST["dday"];
    }
    
    if ($_POST["chrday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["chrday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("chrday", $_POST["chrday"]);
        
        ++$constraintsCount;
        
        $_SESSION["chrday"] = $_POST["chrday"];
    }
    
    $_SESSION["individual_constraints_query"] = $query;
    $_SESSION["individual_constraints_search"] = true;
    
    LOGTEXT("Hakulauseke on : ".$query);
}
    
/**
 *  Palataan takaisin pääsivulle.
 */
if(isset($_POST["logout_search"])) {
        
    LOGTEXT("Palataan <a href=\"main.php\">pääsivulle</a>.");
    
    clear_constraints_fields();
    
    if (!GET_LOGGING())
        
        redirect("main.php");
        
    exit;
}

$limit = intval(RESULTS_IN_PAGE);
$offset = intval($pagenumber * RESULTS_IN_PAGE);

$individual_count = get_individual_count($query);
$pages = ceil($individual_count/RESULTS_IN_PAGE);

LOGTEXT("Sivujen määrä : ".$pages);

create_html_start("Hae henkilö");

create_html_individual_search_header_banner();
create_html_individual_search_upperbanner();

create_html_searchpanel();
create_html_search_count_panel($individual_count);

$individuals = get_individual_database($query, $limit, $offset);

create_html_individual_data_panel($pages, $pagenumber, $individuals);

create_html_end();

/**
 * Haetaan henkilöt tietokannasta
 * 
 */
function get_individual_database($query, $limit, $offset) {
    
    LOGTEXT("GET_INDIVIDUAL_DATABASE : Haetaan henkilötietokanta muistiin\n");
            
    // Haetaan henkilöt tietokannasta
    $individualsql = fetch_individual_database($query, $limit, $offset);
   
    // Muokataan tietokannan lähde lyhenne oikeaksi lähteeksi
    foreach ($individualsql as &$individual) {
        
        $sauce = '';
        
        if($individual["source"] != '') {
                       
            $sourcearray = preg_split("/¤/", $individual["source"]);
                 
            foreach ($sourcearray as $source) {
                
                if ($source != '') {
                    
                    $saucearray = get_source($source);
                    
                    $sauce .= $saucearray[0][0]."<br>";
                }
                $individual["source"] = $sauce;
            }
        }    
    }
        
    return $individualsql;
}

function clear_constraints_fields() {
    
    LOGTEXT("CLEAR_CONSTRAINTS_FIELDS : Tyhjennetään rajauskentät");
    
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
    $_SESSION["moveday"] = "";
    $_SESSION["moveplace"] = "";
    
    $_SESSION["individual_constraints_search"] = false;
    $_SESSION["individual_name_search"] = '';
}

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

?>