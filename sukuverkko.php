<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");
require_once("sql.php");
require_once("html.php");

session_start();

// Käynnistetään ohjelmaa
LOGTEXT("--------Käynnistetään ohjelmaa------------------");

// Tarkistetaan tietokannan taulut
$database_exists = check_database();

// Luodaan taulut jos eivät ole olemassa tai ovat puutteellisia
if (!$database_exists) {
    
    create_tables("familynet_families");
    create_tables("familynet_individuals");
    create_tables("familynet_sources");
}

initialize_search_panels();

LOGTEXT("--------Ajetaan pääohjelma-------------------");
LOGTEXT("Siirrytään <a href=\"main.php\">pääohjelmaan</a>.");

if (!GET_LOGGING())
    redirect("main.php");

function initialize_search_panels() {
        
    LOGTEXT("INITIALIZE_SEARCH_PANELS");
    
    $_SESSION["givn"] = '';
    $_SESSION["surn"] = '';
    $_SESSION["bday"] = '';
    $_SESSION["bplace"] = '';
    $_SESSION["dday"] = '';
    $_SESSION["dplace"] = '';
    $_SESSION["dcause"] = '';
    $_SESSION["occu"] = '';
    $_SESSION["buday"] = '';
    $_SESSION["buplace"] = '';
    $_SESSION["chrday"] = '';
    $_SESSION["chrplace"] = '';
    $_SESSION["moveday"] = '';
    $_SESSION["moveplace"] = '';
    $_SESSION["individual_name"] = '';
    $_SESSION["individual_constraints_search"] = '';
    $_SESSION["individual_name_search"] = '';
}

?>