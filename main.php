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
    
    erase_table(FAMILIES);
    erase_table(INDIVIDUALS);
    erase_table(SOURCES);
    erase_table(STATISTICS);
    erase_table(MARRIAGES);
    
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

/**
 *  Ohjataan lähdesivulle.
 */
if(isset($_POST["sources"])) {
    
    LOGTEXT("Ohjataan <a href=\"sources.php\">lähdesivulle</a>.");
    
    if (!GET_LOGGING()) {
        
        redirect("sources.php");
    }
    exit;
}

    /**
 *  Luodaan tilastotietokanta.
 */
if(isset($_POST["create_statistics"])) {
    
    LOGTEXT("Luodaan tilastotietokanta.");
    
    create_tables(STATISTICS);
    create_statistics_database(); 
    
    LOGTEXT("Ohjataan <a href=\"main.php\">pääsivulle</a>.");
    
    if (!GET_LOGGING()) {
        
        redirect("main.php");
    }
    exit;
}

/**
 *  Luodaan avioliittotietokanta.
 */
if(isset($_POST["create_marriages"])) {
    
    LOGTEXT("Luodaan avioliittotietokanta.");
    
    create_tables(MARRIAGES);
    create_marriages_database();
    
    LOGTEXT("Ohjataan <a href=\"main.php\">pääsivulle</a>.");
    
    if (!GET_LOGGING()) {
        
        redirect("main.php");
    }
    exit;
}

/**
 *  Haetaan avioliitot.
 */
if(isset($_POST["get_marriage"])) {
    
    LOGTEXT("Ohjataan <a href=\"search_marriage.php\">avioliittosivulle</a>.");
       
    if (!GET_LOGGING())
        
        redirect("search_marriage.php");
        
        exit;
}

if(isset($_POST["return_from_source"])) {
    
    LOGTEXT("Ohjataan <a href=\"main.php\">pääsivulle</a>.");
    
    if (!GET_LOGGING()) {
        
        redirect("main.php");
    }
    exit;
}

$_SESSION["individual_count"] = get_individual_count(null);
$_SESSION["family_count"] = get_family_count(null);

create_html_start("Sukuverkko");

create_html_header_panel();
create_html_main_button_panel();
create_html_count_panel($_SESSION["individual_count"],$_SESSION["family_count"]);
create_html_history();
create_html_source();
create_html_copyrigth();

create_html_end();

function create_statistics_database() {
    
    LOGTEXT("CREATE_STATISTICS_DATABASE : Luodaan statistiikka-tietokanta");
    
    for($year = 1700;$year<=date("Y");$year++) {
        
        $statistics = array();
        
        $birthday_count = get_count_by_year("bday",$year);
        $deathday_count = get_count_by_year("dday",$year);
        $marrday_count = get_count_by_year("marday",$year);
        $infantdeath_count = get_infantdeath_count_by_year($year);
        array_push($statistics,$birthday_count);
        array_push($statistics,$deathday_count);
        array_push($statistics,$marrday_count);
        array_push($statistics,$infantdeath_count);
        set_statistics_by_year($year, $statistics);
    }
}

function create_marriages_database() {
    
    LOGTEXT("CREATE_MARRIAGES_DATABASE : Luodaan avioliitto-tietokanta");
    
    // Haetaan lapset families-tietokannasta
    $children = get_children_count();
    
    // Haetaan lasten määrä perheessä tietueesta
    foreach ($children as $child) {
        
        LOGARRAY($child);
        $child_array = array();
        $child_count = substr_count($child[0], DATA_DELIMITER);
        array_push($child_array, $child[1]);
        array_push($child_array, $child_count);
        set_children_count($child_array);
        unset($child_array);
    }
    
    // Haetaan puolisoiden tiedot vihkimisineen
    $marriages = get_marriages();
    
    // Haetaan avioliitot tietueesta
    foreach ($marriages as $marriage) {
        
        LOGARRAY($marriage);
        set_marriage($marriage);
    }
}

?>