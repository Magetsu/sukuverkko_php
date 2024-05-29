<?php

require_once("logging.php");
require_once("html.php");
require_once("sql.php");
require_once("create_query.php");

session_start();

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
        
    clear_individual_constraints_fields();
    
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
        
        $query .= get_constraint_query("givn", $constraintsCount);         
        ++$constraintsCount;       
    }
    
    if ($_POST["surn"]) {
                
        $query .= get_constraint_query("surn", $constraintsCount);       
        ++$constraintsCount;
    }

    if ($_POST["bplace"]) {
                
        $query .= get_constraint_query("bplace", $constraintsCount);        
        ++$constraintsCount;
    }
    
    if ($_POST["dplace"]) {
        
        $query .= get_constraint_query("dplace", $constraintsCount);
        ++$constraintsCount;
    }
    
    if ($_POST["dcause"]) {
        
        $query .= get_constraint_query("dcause", $constraintsCount);
        ++$constraintsCount;
    }
    
    if ($_POST["buplace"]) {
        
        $query .= get_constraint_query("buplace", $constraintsCount);
        ++$constraintsCount;
    }
    
    if ($_POST["chrplace"]) {
        
        $query .= get_constraint_query("chrplace", $constraintsCount);
        ++$constraintsCount;
    }

    if ($_POST["occu"]) {
        
        $query .= get_constraint_query("occu", $constraintsCount);
        ++$constraintsCount;
    }
    
    if ($_POST["move"]) {
        
        $query .= get_constraint_query("move", $constraintsCount);
        ++$constraintsCount;        
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
    
    clear_individual_constraints_fields();
    
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

create_html_individual_search_button_panel();

create_html_individual_search_constraints_panel();
create_html_individual_search_help_banner();

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

?>