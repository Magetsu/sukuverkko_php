<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("constants.php");
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
        
        $connection = new PDO(HOST,USERNAME,PASSWORD);
        
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
    
    $table_families = FAMILIES;
    $table_individuals = INDIVIDUALS;
    $table_sources = SOURCES;
    $table_statistics = STATISTICS;
    
    $sql = 'SHOW TABLES';
    
    LOGTEXT("CHECK_DATABASE : Luodaan kysely : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
    $array = $query->fetchAll(PDO::FETCH_COLUMN, 0);
    
    //LOGARRAY($array);
    
    if (in_array($table_families,$array) && in_array($table_individuals,$array) && in_array($table_sources,$array) && in_array($table_statistics,$array)) {
        
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

    $database = DB;
    
    // Poistetaan taulut ennenkuin luodaan uudet taulut
    erase_table($table_name);
    
    switch  ($table_name) {
        case FAMILIES:

            // Luodaan nyt uusi perhetietokanta.
            $sql="CREATE TABLE IF NOT EXISTS $database.$table_name ( xref VARCHAR(10),
                                                                  husb VARCHAR(10),
                												  wife VARCHAR(10),
                												  marday DATE,
                												  marplace VARCHAR(100),
													              child VARCHAR(1000) )";
            break;
        case INDIVIDUALS:
            
            // Luodaan nyt uusi henkilötietokanta.
            $sql="CREATE TABLE IF NOT EXISTS $database.$table_name ( xref VARCHAR(10),
                                                                     givn VARCHAR(100),
                													 surn VARCHAR(100),
                													 sex VARCHAR(1),
                													 occu VARCHAR(100),
                													 bday DATE,
                													 bplace VARCHAR(100),
                													 dday DATE,
                													 dplace VARCHAR(100),
                													 dcause VARCHAR(100),
                													 buday DATE,
                													 buplace VARCHAR(100),
                													 chrday DATE,
                													 chrplace VARCHAR(100),
                													 note VARCHAR(10000),
                													 move VARCHAR(100),
                													 isdead INT(1),
                													 source VARCHAR(100) )";
            break;
        case SOURCES:
            
            // Luodaan nyt uusi lähdetietokanta.
            $sql="CREATE TABLE IF NOT EXISTS $database.$table_name ( xref VARCHAR(10),
													               name VARCHAR(100) )";
            break;
        case STATISTICS:
            
            // Luodaan nyt uusi statistiikkatietokanta.
            $sql="CREATE TABLE IF NOT EXISTS $database.$table_name ( year VARCHAR(10),
                                                                    bircount VARCHAR(10),
													                marcount VARCHAR(10),
                                                                    detcount VARCHAR(10),
                                                                    infdcount VARCHAR(10) )";
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

    $database = DB;
    
    $sql = "DROP TABLE IF EXISTS $database.$table_name";
    
    $query = $conn->prepare($sql);
    $query->execute();
}

//
//  Haetaan henkilöiden määrä tietokannassa
//
function get_individual_count($query) {
    
    LOGTEXT("GET_INDIVIDUAL_COUNT : Haetaan henkilöiden määrä.");
    
    $conn = connect_to_database();
    
    $database = DB;
    $table = INDIVIDUALS;
    
    if(!isset($_SESSION["user_id"])) {
        
        LOGTEXT("GET_INDIVIDUAL_COUNT : Haetaan pelkästään kuolleiden henkilöiden määrä (Ei olla kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = "SELECT COUNT(*) FROM $database.$table WHERE isdead='1' AND ($query)";
            
        } else {
            
            $sql = "SELECT COUNT(*) FROM $database.$table WHERE isdead='1'";
        }
    } else if(isset($_SESSION["user_id"])) {
        
        LOGTEXT("GET_INDIVIDUAL_COUNT : Haetaan kaikkien henkilöiden määrä (Ollaan kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = "SELECT COUNT(*) FROM $database.$table WHERE $query";
            
        } else {
            
            $sql = "SELECT COUNT(*) FROM $database.$table";
            
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
    
    $database = DB;
    $table = FAMILIES;
    
    $sql = "SELECT COUNT(*) FROM $database.$table";
    
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
    
    $database = DB;
    $table = INDIVIDUALS;
    
    $sql = "INSERT INTO $database.$table (xref,givn,surn,sex,occu,bday,bplace,dday,dplace,dcause,buday,buplace,chrday,chrplace,note,move,isdead,source) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $query = $conn->prepare($sql);
    $query->execute([$record[0],$record[1],$record[2],$record[3],$record[4],$record[5],$record[6],$record[7],$record[8],$record[9],$record[10],$record[11],$record[12],$record[13],$record[14],$record[15],$record[16],$record[17]]);
}

//
// Kirjoitetaan perhe tietokantaan
//
function import_family_to_database($record) {
    
    LOGTEXT("IMPORT_FAMILY_TO_DATABASE : Kirjoitetaan perhe tietokantaan : ".LOGARRAY($record));
    
    $conn = connect_to_database();
    
    $database = DB;
    $table = FAMILIES;
    
    $sql = "INSERT INTO $database.$table (xref,husb,wife,marday,marplace,child) values ( ?, ?, ?, ?, ?, ?)";
    
    $query = $conn->prepare($sql);
    $query->execute([$record[0],$record[1],$record[2],$record[3],$record[4],$record[5]]);
}

//
// Kirjoitetaan lähde tietokantaan
//
function import_source_to_database($record) {
    
    LOGTEXT("IMPORT_SOURCE_TO_DATABASE : Kirjoitetaan lähde tietokantaan : ".LOGARRAY($record));
    
    $conn = connect_to_database();
    
    $database = DB;
    $table = SOURCES;
    
    $sql = "INSERT INTO $database.$table (xref,name) values ( ?, ?)";
    
    $query = $conn->prepare($sql);
    $query->execute([$record[0],$record[1]]);
}

//
// Hakee tietokannasta henkilöt
//
function fetch_individual_database($query, $limit, $offset) {
    
    LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan henkilötietokantatiedot : ".$offset." - ".$offset+$limit);
    
    $connect = connect_to_database();
    
    $database = DB;
    $table = INDIVIDUALS;
    
    if(!isset($_SESSION["user_id"])) {
        
        LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan vain kuolleiden henkilötietokantatiedot (Ei olla kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = "SELECT * FROM $database.$table WHERE isdead='1' AND ($query) LIMIT $limit OFFSET $offset";
            
        } else {
            
            $sql = "SELECT * FROM $database.$table WHERE isdead='1' LIMIT $limit OFFSET $offset";
            
        }
    } else if(isset($_SESSION["user_id"])) {
        
        LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan kaikkien henkilötietokantatiedot (Ollaan kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = "SELECT * FROM $database.$table WHERE $query LIMIT $limit OFFSET $offset";
            
        } else {
            
            $sql = "SELECT * FROM $database.$table LIMIT $limit OFFSET $offset";
            
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
    
    $database = DB;
    $table = SOURCES;
    
    $sql = "SELECT name FROM $database.$table WHERE magetsu.$table.xref = '$source'";
    
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
    
    $database = DB;
    $table = INDIVIDUALS;
    
    $sql = "SELECT COUNT(*) FROM $database.$table";
    
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

    $database = DB;
    
    switch ($data) {
        case 'bday':
        case 'dday':
            $table = INDIVIDUALS;
            break;
        case 'marday':
            $table = FAMILIES;
            break;
        default:
            return null;
    }
   
    $sql = "SELECT COUNT(*) FROM $database.$table WHERE YEAR($data) = '".$year."'";
      
    $query = $connect->prepare($sql);
    $query->execute();
    $count = $query->fetchColumn();
    
    return $count;
}

function get_infantdeath_count_by_year($year) {
    
    $connect = connect_to_database();
    
    $database = DB;
    $table = INDIVIDUALS;
    
    $sql = "SELECT COUNT(*) FROM $database.$table WHERE YEAR(dday) = '".$year."' AND DATEDIFF(dday, bday) < '365'";
   
    $query = $connect->prepare($sql);
    $query->execute();
    $count = $query->fetchColumn();
    
    return $count;
}

//
//  Asetetaan vuoden statistiikka tietokantaan
//
function set_statistics_by_year($year, $statistics) {
 
    $conn = connect_to_database();
    
    $database = DB;
    $table = STATISTICS;
    
    $sql = "INSERT INTO $database.$table (year,bircount,marcount,detcount,infdcount) values ( $year, $statistics[0], $statistics[1], $statistics[2], $statistics[3])";
 
    LOGTEXT("SQL-clause : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
}

//
//  Asetetaan vuoden statistiikka tietokantaan
//
function get_statistics_by_year($year) {
    
    $conn = connect_to_database();
    
    $database = DB;
    $table = STATISTICS;
 
    $sql = "SELECT * FROM $database.$table WHERE year = $year";
    
    //LOGTEXT("SQL-clause : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
    $statistics = $query->fetchAll();

    //LOGARRAY($statistics);
    
    return $statistics;
}
?>