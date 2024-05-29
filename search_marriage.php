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
    
    $query = $_SESSION["marriage_constraints_query"];
}

/*
 * Haetaan rajoitushaulla.
 */
if(isset($_POST["marriage_search"])) {
    
    $constraintsCount = 0;
    clear_marriage_constraints_fields();

    if ($_POST["husbgivn"]) {
        
        $query .= get_constraint_query("husbgivn", $constraintsCount);
        ++$constraintsCount;
    }

    if ($_POST["husbsurn"]) {
        
        $query .= get_constraint_query("husbsurn", $constraintsCount);
        ++$constraintsCount;
    }

    if ($_POST["wifegivn"]) {
        
        $query .= get_constraint_query("wifegivn", $constraintsCount);
        ++$constraintsCount;
    }
    
    if ($_POST["wifesurn"]) {
        
        $query .= get_constraint_query("wifesurn", $constraintsCount);
        ++$constraintsCount;
    }

    if ($_POST["husbbplace"]) {
        
        $query .= get_constraint_query("husbbplace", $constraintsCount);
        ++$constraintsCount;
    }
    
    if ($_POST["husbdplace"]) {
        
        $query .= get_constraint_query("husbdplace", $constraintsCount);
        ++$constraintsCount;
    }
    
    if ($_POST["wifebplace"]) {
        
        $query .= get_constraint_query("wifebplace", $constraintsCount);
        ++$constraintsCount;
    }
    
    if ($_POST["wifedplace"]) {
        
        $query .= get_constraint_query("wifedplace", $constraintsCount);
        ++$constraintsCount;
    }

    if ($_POST["marplace"]) {
        
        $query .= get_constraint_query("marplace", $constraintsCount);
        ++$constraintsCount;
    }
/*    
    if ($_POST["husbbday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["husbbday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("husbbday", $_POST["husbbday"]);
        
        ++$constraintsCount;
        
        $_SESSION["husbbday"] = $_POST["husbbday"];
    }
    
    if ($_POST["wifebday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["wifebday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("wifebday", $_POST["wifebday"]);
        
        ++$constraintsCount;
        
        $_SESSION["wifebday"] = $_POST["wifebday"];
    }
    
    if ($_POST["husbdday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["husbdday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("husbdday", $_POST["husbdday"]);
        
        ++$constraintsCount;
        
        $_SESSION["husbdday"] = $_POST["husbdday"];
    }
    
    if ($_POST["wifedday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["wifedday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("wifedday", $_POST["wifedday"]);
        
        ++$constraintsCount;
        
        $_SESSION["wifedday"] = $_POST["wifedday"];
    }
*/    
    if ($_POST["marday"]) {
        
        LOGTEXT("Haetaan rajauksella nimi : ".$_POST["marday"]);
        
        if ($constraintsCount > 0) {
            
            $query .= " AND ";
        }
        
        $query .= create_time_search_query("marday", $_POST["marday"]);
        
        ++$constraintsCount;
        
        $_SESSION["marday"] = $_POST["marday"];
    }
    
    $_SESSION["marriage_constraints_query"] = $query;
    
    LOGTEXT("Hakulauseke on : ".$query);
}
    
/**
 *  Palataan takaisin pääsivulle.
 */
if(isset($_POST["logout_marriage"])) {
    
    LOGTEXT("Palataan <a href=\"main.php\">pääsivulle</a>.");
        
    clear_marriage_constraints_fields();
    
    if (!GET_LOGGING())
        
        redirect("main.php");
        
        exit;
}

$limit = intval(RESULTS_IN_PAGE);
$offset = intval($pagenumber * RESULTS_IN_PAGE);

$family_count = get_family_count($query);
$pages = ceil($family_count/RESULTS_IN_PAGE);

LOGTEXT("Sivujen määrä : ".$pages);

create_html_start("Hae avioliitto");

create_html_marriage_search_button_panel();
create_html_marriage_search_constraints_panel();

create_html_individual_search_help_banner();

$marriages = get_marriage_database($query, $limit, $offset);

create_html_marriage_data_panel($pages, $pagenumber, $marriages);

create_html_end();

/**
 * Haetaan avioliitot tietokannasta
 *
 */
function get_marriage_database($query, $limit, $offset) {
    
    LOGTEXT("GET_MARRIAGE_DATABASE : Haetaan avioliittotietokanta muistiin\n");
    
    // Haetaan henkilöt tietokannasta
    $marriagesql = fetch_marriage_database($query, $limit, $offset);
        
    return $marriagesql;
}

?>