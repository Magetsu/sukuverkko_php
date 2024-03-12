<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");

//
//  Yhdistetään tietokantaan
//
function connect_to_database() {
    
    // Staattinen muuttuja ei katoa vaikka funktiosta palataankin.
    // Alustetaan muuttuja false-arvolla.
    static $connection = false;
    
    if($connection != false) {
        
        //LOGTEXT("CONNECT_TO_DATABASE : Käytetään olemassa olevaa yhteyttä.");
        
        return $connection;
        
    }
    
    LOGTEXT("CONNECT_TO_DATABASE : Luodaan uusi yhteys.");
    
    // Jos yhteyttä ei ole vielä avattu, avataan se.
    // Jos yhteyden avaaminen ei onnistu, heitetään
    // poikkeus, joten se pitää napata.
    try {
        
        $connection = new PDO("mysql:host=db1.n.kapsi.fi;dbname=magetsu","magetsu","FgQiYWtkRX");
        
    } catch (PDOException $e) {
        
        exit("Tietokantavirhe: " . $e->getMessage());
    }
    
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->exec("set names utf8");
    
    LOGTEXT("CONNECT_TO_DATABASE : Yhteys tietokantaan saatu.");
    
    return $connection;
}

//
//  Tarkistetaan onko tietokannat olemassa.
//
function check_database() {
    
    LOGTEXT("CHECK_DATABASE : Tarkistetaan taulut tietokannasta.");
    
    $conn = connect_to_database();
    
    $table_families = 'familynet_families';
    $table_individuals = 'familynet_individuals';
    $table_sources = 'familynet_sources';
    
    $sql = 'SHOW TABLES';
    
    LOGTEXT("CHECK_DATABASE : Luodaan kysely : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
    $array = $query->fetchAll(PDO::FETCH_COLUMN, 0);
    
    //LOGARRAY($array);
    
    if (in_array($table_families,$array) && in_array($table_individuals,$array) && in_array($table_sources,$array)) {
        
        LOGTEXT("CHECK_DATABASE : Taulut ovat olemassa tietokannassa.");
        return true;
        
    } else {
        
        LOGTEXT("CHECK_DATABASE : Tauluja ei ole tai osa tauluista puuttuu tietokannasta.");
        return false;
    }
}

//
//  Luodaan tarvittavat taulut tietokantaan
//
function create_tables($table_name) {
    
    LOGTEXT("CREATE_TABLES : Luodaan taulut tietokantaan.");
    
    $conn = connect_to_database();
    
    erase_table($table_name);
    
    switch  ($table_name) {
        case "familynet_families":

            // Luodaan nyt uusi perhetietokanta.
            $sql="CREATE TABLE IF NOT EXISTS magetsu.familynet_families ( xref VARCHAR(10),
                                                                  husb VARCHAR(10),
                												  wife VARCHAR(10),
                												  marday VARCHAR(100),
                												  marplace VARCHAR(100),
													              child VARCHAR(1000) )";
            break;
        case "familynet_individuals":
            
            // Luodaan nyt uusi henkilötietokanta.
            $sql="CREATE TABLE IF NOT EXISTS magetsu.familynet_individuals ( xref VARCHAR(10),
                                                                     givn VARCHAR(100),
                													 surn VARCHAR(100),
                													 sex VARCHAR(1),
                													 occu VARCHAR(100),
                													 bday VARCHAR(100),
                													 bplace VARCHAR(100),
                													 dday VARCHAR(100),
                													 dplace VARCHAR(100),
                													 dcause VARCHAR(100),
                													 buday VARCHAR(100),
                													 buplace VARCHAR(100),
                													 chrday VARCHAR(100),
                													 chrplace VARCHAR(100),
                													 note VARCHAR(10000),
                													 move VARCHAR(100),
                													 isdead INT(1),
                													 source VARCHAR(100) )";
            break;
        case "familynet_sources":
            
            // Luodaan nyt uusi lähdetietokanta.
            $sql="CREATE TABLE IF NOT EXISTS magetsu.familynet_sources ( xref VARCHAR(10),
													             name VARCHAR(100) )";
            break;
        default: 
            break;
    }
    LOGTEXT("CREATE_TABLES : Luodaan kysely : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
}

//
//  Poistetaan taulukot tietokannasta
//
function erase_table($table_name) {

    LOGTEXT("ERASE_TABLE : Poistetaan taulukko ".$table_name." tietokannasta.");
    
    $conn = connect_to_database();

    $sql = "DROP TABLE IF EXISTS magetsu.$table_name";
    
    $query = $conn->prepare($sql);
    $query->execute();
}

//
//  Haetaan henkilöiden määrä tietokannassa
//
function get_individual_count($query) {
    
    LOGTEXT("GET_INDIVIDUAL_COUNT : Haetaan henkilöiden määrä.");
    
    $conn = connect_to_database();
    
    $table = 'magetsu.familynet_individuals';
    
    if(!isset($_SESSION["user_id"])) {
        
        LOGTEXT("GET_INDIVIDUAL_COUNT : Haetaan pelkästään kuolleiden henkilöiden määrä (Ei olla kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = "SELECT COUNT(*) FROM $table WHERE isdead='1' AND ($query)";
            
        } else {
            
            $sql = "SELECT COUNT(*) FROM $table WHERE isdead='1'";
        }
    } else if(isset($_SESSION["user_id"])) {
        
        LOGTEXT("GET_INDIVIDUAL_COUNT : Haetaan kaikkien henkilöiden määrä (Ollaan kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = "SELECT COUNT(*) FROM $table WHERE $query";
            
        } else {
            
            $sql = "SELECT COUNT(*) FROM $table";
            
        }
    }
    
    $query = $conn->prepare($sql);
    $query->execute();
    $count = $query->fetchColumn();
    
    LOGTEXT("GET_INDIVIDUAL_COUNT : Henkilöiden määrä : ".$count);
    
    return $count;
}

//
//  Haetaan perheiden määrä tietokannassa
//
function get_family_count() {
    
    LOGTEXT("GET_FAMILY_COUNT : Haetaan perheiden määrä.");
    
    $conn = connect_to_database();
    
    $table = 'magetsu.familynet_families';
    
    $sql = "SELECT COUNT(*) FROM $table";
    
    $query = $conn->prepare($sql);
    $query->execute();
    $count = $query->fetchColumn();
    
    LOGTEXT("GET_FAMILY_COUNT : perheiden määrä : ".$count);
    
    return $count;
}

//
// Kirjoitetaan henkilö tietokantaan
//
function import_individual_to_database($record) {
    
    LOGTEXT("IMPORT_INDIVIDUAL_TO_DATABASE : Kirjoitetaan henkilö tietokantaan");
    
    $conn = connect_to_database();
    
    $table = 'magetsu.familynet_individuals';
    $sql = "INSERT INTO $table (xref,givn,surn,sex,occu,bday,bplace,dday,dplace,dcause,buday,buplace,chrday,chrplace,note,move,isdead,source) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $query = $conn->prepare($sql);
    $query->execute([$record[0],$record[1],$record[2],$record[3],$record[4],$record[5],$record[6],$record[7],$record[8],$record[9],$record[10],$record[11],$record[12],$record[13],$record[14],$record[15],$record[16],$record[17]]);
}

//
// Kirjoitetaan perhe tietokantaan
//
function import_family_to_database($record) {
    
    LOGTEXT("IMPORT_FAMILY_TO_DATABASE : Kirjoitetaan perhe tietokantaan : ".LOGARRAY($record));
    
    $conn = connect_to_database();
    
    $table = 'magetsu.familynet_families';
    $sql = "INSERT INTO $table (xref,husb,wife,marday,marplace,child) values ( ?, ?, ?, ?, ?, ?)";
    
    $query = $conn->prepare($sql);
    $query->execute([$record[0],$record[1],$record[2],$record[3],$record[4],$record[5]]);
}

//
// Kirjoitetaan lähde tietokantaan
//
function import_source_to_database($record) {
    
    LOGTEXT("IMPORT_SOURCE_TO_DATABASE : Kirjoitetaan lähde tietokantaan : ".LOGARRAY($record));
    
    $conn = connect_to_database();
    
    $table = 'magetsu.familynet_sources';
    $sql = "INSERT INTO $table (xref,name) values ( ?, ?)";
    
    $query = $conn->prepare($sql);
    $query->execute([$record[0],$record[1]]);
}

//
// Hakee tietokannasta henkilöt
//
function fetch_individual_database($query, $limit, $offset) {
    
    LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan henkilötietokantatiedot : ".$offset." - ".$offset+$limit);
    
    $connect = connect_to_database();
    
    $table = 'magetsu.familynet_individuals';
    
    if(!isset($_SESSION["user_id"])) {
        
        LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan vain kuolleiden henkilötietokantatiedot (Ei olla kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = "SELECT * FROM $table WHERE isdead='1' AND ($query) LIMIT $limit OFFSET $offset";
            
        } else {
            
            $sql = "SELECT * FROM $table WHERE isdead='1' LIMIT $limit OFFSET $offset";
            
        }
    } else if(isset($_SESSION["user_id"])) {
        
        LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan kaikkien henkilötietokantatiedot (Ollaan kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = "SELECT * FROM $table WHERE $query LIMIT $limit OFFSET $offset";
            
        } else {
            
            $sql = "SELECT * FROM $table LIMIT $limit OFFSET $offset";
            
        }
    }
    
    LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Kysely on : ".$sql);
    
    $query = $connect->prepare($sql);
    $query->execute();
    $array = $query->fetchAll();
    
    return $array;
}

//
// Korvataan lähteen viittaus selkokielisellä tekstillä
//
function get_source($source) {
        
    $conn = connect_to_database();
    
    $source_table = 'magetsu.familynet_sources';
    
    $sql = "SELECT name FROM $source_table WHERE $source_table.xref = '$source'";
    
    $query = $conn->prepare($sql);
    $query->execute();
    $array = $query->fetchAll();
    
    return $array;
}

//
//  Haetaan henkilöiden määrä tietokannasta statistiikkasivuja varten
//
function get_individual_statistics_count() {
    
    LOGTEXT("GET_INDIVIDUAL_COUNT_STATISTICS : Haetaan henkilöiden määrä");
    
    $connect = connect_to_database();
    
    $table = 'magetsu.familynet_individuals';
    
    $sql = "SELECT COUNT(*) FROM $table";
    
    $query = $connect->prepare($sql);
    $query->execute();
    $count = $query->fetchColumn();
    
    LOGTEXT("GET_INDIVIDUAL_COUNT_STATISTICS : Henkilöiden määrä : ".$count);
    
    return $count;
}

//
//  Haetaan henkilöiden määrä tietylle vuodelle
//
function get_count_by_year($data, $year) {
    
    $connect = connect_to_database();
    
    $source_table = 'magetsu.familynet_individuals';
    
    $sql = "SELECT COUNT(*) FROM $source_table WHERE $data LIKE '%$year'";
    
    $query = $connect->prepare($sql);
    $query->execute();
    $count = $query->fetchColumn();
    
    return $count;
}
?>