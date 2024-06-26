<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("constants.php");
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
    
    LOGTEXT("--------Luodaan tietokannat------------------");
    
    create_tables(FAMILIES);
    create_tables(INDIVIDUALS);
    create_tables(SOURCES);
    create_tables(STATISTICS);
    create_tables(MARRIAGES);
}

initialize_search_panels();

LOGTEXT("--------Ajetaan pääohjelma-------------------");
LOGTEXT("Siirrytään <a href=\"main.php\">pääohjelmaan</a>.");

if (!GET_LOGGING())
    redirect("main.php");
    
function initialize_search_panels() {
        
        LOGTEXT("INITIALIZE_SEARCH_PANELS : Tyhjennetään hakukentät");
        
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
        $_SESSION["move"] = '';
        $_SESSION["individual_name"] = '';
        $_SESSION["individual_constraints_search"] = '';
        $_SESSION["individual_name_search"] = '';
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
        $_SESSION["marplace"] = "";}
    

?>