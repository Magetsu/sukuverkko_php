<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("constants.php");
require_once("sql.php");
require_once("html.php");
require_once("logging.php");
require_once("gedcom_parser.php");

/**
 *  Ladataan gedcom tietokantaan.
 */
if(isset($_POST["upload_gedcom_data"])) {
    
    LOGTEXT("Ladataan gedcom tietokantaan.");

    $gedcom_data = $_FILES['gedcom_data']['tmp_name'];
    
    // Tarkistetaan saatiinko yhtään tiedostoa.
    if (!file_exists($gedcom_data)) {
        
        // Ei saatu joten palataan pääsivulle.
        LOGTEXT("Ei ladattavia tiedostoja. Poistutaan...");
        LOGTEXT("<p>Palaa <a href=\"sukuverkko.php\">pääsivulle</a>.</p>");
        
        if (!GET_LOGGING()) {
            
            redirect('main.php');
            exit;
        }
    }
    
    // Tarkistetaan ladattiinko gedcom-tiedosto.
    if(file_exists($gedcom_data) && is_uploaded_file($gedcom_data)) {
        
        LOGTEXT("Filename: " . $_FILES['gedcom_data']['name']);
        LOGTEXT("Type : " . $_FILES['gedcom_data']['type']);
        LOGTEXT("Size : " . $_FILES['gedcom_data']['size']);
        LOGTEXT("Temp name: " . $_FILES['gedcom_data']['tmp_name']);
        LOGTEXT("Error : " . $_FILES['gedcom_data']['error']);
    }

    // Poistetaan vanhat taulukot koska otetaan uudet tiedot taulukoihin
    create_tables(FAMILIES);
    create_tables(INDIVIDUALS);
    create_tables(SOURCES);
    create_tables(STATISTICS);
    create_tables(MARRIAGES);
    
    // Parsitaan gedcom-tiedosto
    parse_file($gedcom_data);
    
    create_index(INDIVIDUALS);
    create_index(FAMILIES);
    
    LOGTEXT("<p>Palaa <a href=\"main.php\">pääsivulle</a>.</p>");
    
    if (!GET_LOGGING())
        
        redirect('main.php');

    exit;
}

create_html_start("Tuo Gedcom-tiedosta");

create_html_upload();

create_html_end();

?>