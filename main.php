<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");
require_once("html.php");
require_once("sql.php");

session_start();

/**
 *  Kirjaudutaan sisään.
 */
if(isset($_POST["login_credit"])) {
    
    LOGTEXT("Ohjataan <a href=\"login.php\">kirjautumissivulle</a>.");
    
    if (!GET_LOGGING()) {
        
        // Jos käyttäjä ei ole kirjautunut ohjataan käyttäjä kirjautumissivulle
        redirect("login.php");
    }
    exit;
}

/**
 *  Kirjaudutaan ulos.
 */
if(isset($_POST["logout_credit"])) {
    
    // Tuhotaan ensin istunnon eväste.
    if (isset($_COOKIE[session_name()]))
        
        setcookie(session_name(), '', time()-42000, '/');
        
        // Ja sitten hävitetään kaikki istuntodata.
        session_destroy();
        
        LOGTEXT("Kirjaudutaan <a href=\"main.php\">ulos</a>.");
        
        if (!GET_LOGGING())
            
            // Jos käyttäjä ei ole kirjautunut ohjataan käyttäjä pääsivulle ilman kirjautumista
            redirect("main.php");
            exit;
}

/**
 *  Ohjataan gedcom-lataamissivulle.
 */
if(isset($_POST["upload_gedcom"])) {
    
    LOGTEXT("Ohjataan <a href=\"upload.php\">gedcom-lataamissivulle</a>.");
    
    if (!GET_LOGGING()) {
        
        redirect("upload.php");
    }
    exit;
}

/**
 *  Poistetaan tietokannat.
 */
if(isset($_POST["erase_tables"])) {
    
    LOGTEXT("Poistetaan taulukot tietokannasta.");
    
    erase_table("familynet_families");
    erase_table("familynet_individuals");
    erase_table("familynet_sources");

    LOGTEXT("Ohjataan <a href=\"main.php\">pääsivulle</a>.");

    if (!GET_LOGGING()) {
        
        redirect("main.php");
    }
    exit;
}

/**
 *  Ohjataan gedcom-lataamissivulle.
 */
if(isset($_POST["search_individual"])) {
    
    LOGTEXT("Ohjataan <a href=\"search_individual.php\">hakusivulle</a>.");
    
    if (!GET_LOGGING()) {
        
        redirect("search_individual.php");
    }
    exit;
}

/**
 *  Ohjataan tilastosivulle.
 */
if(isset($_POST["statistics"])) {
    
    LOGTEXT("Ohjataan <a href=\"statistics.php\">tilastosivulle</a>.");
    
    if (!GET_LOGGING()) {
        
        redirect("statistics.php");
    }
    exit;
}

$_SESSION["individual_count"] = get_individual_count(null);
$_SESSION["family_count"] = get_family_count(null);

create_html_start("Sukuverkko");

create_html_header_panel();
create_html_main_upperbanner();
create_html_count_panel($_SESSION["individual_count"],$_SESSION["family_count"]);

create_html_end();
?>