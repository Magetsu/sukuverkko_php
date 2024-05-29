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
            
    LOGTEXT("CHECK_DATABASE : Luodaan kysely : ".SQL_SHOW_TABLES);
    
    $query = $conn->prepare(SQL_SHOW_TABLES);
    $query->execute();
    $array = $query->fetchAll(PDO::FETCH_COLUMN, 0);
    
    //LOGARRAY($array);
    
    if (in_array(FAMILIES,$array) && in_array(INDIVIDUALS,$array) && in_array(SOURCES,$array) && in_array(STATISTICS,$array)) {
        
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
    
    LOGTEXT("CREATE_TABLES : Luodaan taulut tietokantaan".$table_name.".");
    
    $conn = connect_to_database();

    $database = DB;
    
    // Poistetaan taulut ennenkuin luodaan uudet taulut
    erase_table($table_name);
    
    switch  ($table_name) {
        case FAMILIES:

            // Luodaan nyt uusi perhetietokanta.
            $sql=SQL_CREATE_TABLE.$database.".".$table_name.SQL_FAMILY_TABLE;
            
            break;
        case INDIVIDUALS:
            
            // Luodaan nyt uusi henkilötietokanta.
            $sql=SQL_CREATE_TABLE.$database.".".$table_name.SQL_INDIVIDUAL_TABLE;
            
            break;
        case SOURCES:
            
            // Luodaan nyt uusi lähdetietokanta.
            $sql=SQL_CREATE_TABLE.$database.".".$table_name.SQL_SOURCE_TABLE;
            break;
        case STATISTICS:
            
            // Luodaan nyt uusi statistiikkatietokanta.
            $sql=SQL_CREATE_TABLE.$database.".".$table_name.SQL_STATISTICS_TABLE;
            
            break;
        case MARRIAGES:

            // Luodaan nyt uusi avioliittotietokanta.
            $sql=SQL_CREATE_TABLE.$database.".".$table_name.SQL_MARRIAGE_TABLE;

            break;
        default: 
            break;
    }
    LOGTEXT("CREATE_TABLES : Luodaan kysely : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
}

//
//  Indeksoidaan taulut tietokannasta
//
function create_index($table_name) {

    LOGTEXT("CREATE_INDEX : Indeksoidaan taulut tietokannasta ".$table_name.".");
    
    $conn = connect_to_database();
    
    $database = DB;
    $index = "index_".$table_name;
    
    switch ($table_name) {
        case FAMILIES:
            
            $sql = SQL_CREATE_INDEX.$index." ON ".$database.$table_name.SQL_FAMILY_INDEX;
            
            break;
        case INDIVIDUALS:
            
            $sql = SQL_CREATE_INDEX.$index." ON ".$database.$table_name.SQL_INDIVIDUAL_INDEX;
            
            break;
        default:
            return null;
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
    
    $sql = SQL_DROP_TABLE.$database.$table_name;
    
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
            
            $sql = SQL_SELECT_COUNT.$database.".".$table." WHERE isdead='1' AND ($query)";
            
        } else {
            
            $sql = SQL_SELECT_COUNT.$database.".".$table." WHERE isdead='1'";
        }
    } else if(isset($_SESSION["user_id"])) {
        
        LOGTEXT("GET_INDIVIDUAL_COUNT : Haetaan kaikkien henkilöiden määrä (Ollaan kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = SQL_SELECT_COUNT.$database.".".$table." WHERE $query";
            
        } else {
            
            $sql = SQL_SELECT_COUNT.$database.".".$table;
            
        }
    }
    
    LOGTEXT("CREATE_TABLES : Luodaan kysely : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
    $count = $query->fetchColumn();
    
    LOGTEXT("GET_INDIVIDUAL_COUNT : Henkilöiden määrä : ".$count);
    
    return $count;
}

//
//  Haetaan perheiden määrä tietokannassa
//
function get_family_count($query) {
    
    LOGTEXT("GET_FAMILY_COUNT : Haetaan perheiden määrä.");
    
    $conn = connect_to_database();
    
    $database = DB;
    $table = MARRIAGES;
    
    if(!isset($_SESSION["user_id"])) {
        
        LOGTEXT("GET_FAMILY_COUNT : Haetaan vain kuolleiden avioparien tietokantatiedot (Ei olla kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = SQL_SELECT_COUNT.$database.".".$table." WHERE husbisdead='1' AND wifeisdead='1' AND ($query)";
            
        } else {
            
            $sql = SQL_SELECT_COUNT.$database.".".$table." WHERE husbisdead='1' AND wifeisdead='1'";
            
        }
    } else if(isset($_SESSION["user_id"])) {
        
        LOGTEXT("GET_FAMILY_COUNT : Haetaan kaikkien avioparien tietokantatiedot (Ollaan kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = SQL_SELECT_COUNT.$database.".".$table." WHERE $query";
            
        } else {
            
            $sql = SQL_SELECT_COUNT.$database.".".$table;
            
        }
    }
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
    
    $sql = SQL_INSERT_INTO.$database.".".$table.SQL_INSERT_INDIVIDUAL;
    
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
    
    $sql = SQL_INSERT_INTO.$database.".".$table.SQL_INSERT_FAMILY;
    
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
    
    $sql = SQL_INSERT_INTO.$database.".".$table.SQL_INSERT_SOURCE;
    
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
            
            $sql = SQL_SELECT_FROM."$database.$table WHERE isdead='1' AND ($query) LIMIT $limit OFFSET $offset";
            
        } else {
            
            $sql = SQL_SELECT_FROM."$database.$table WHERE isdead='1' LIMIT $limit OFFSET $offset";
            
        }
    } else if(isset($_SESSION["user_id"])) {
        
        LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan kaikkien henkilötietokantatiedot (Ollaan kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = SQL_SELECT_FROM."$database.$table WHERE $query LIMIT $limit OFFSET $offset";
            
        } else {
            
            $sql = SQL_SELECT_FROM."$database.$table LIMIT $limit OFFSET $offset";
            
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
    
    $sql = SQL_SELECT_COUNT.$database.".".$table;
    
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
   
    $sql = SQL_SELECT_COUNT."$database.$table WHERE YEAR($data) = '".$year."'";
      
    $query = $connect->prepare($sql);
    $query->execute();
    $count = $query->fetchColumn();
    
    return $count;
}

function get_infantdeath_count_by_year($year) {
    
    $connect = connect_to_database();
    
    $database = DB;
    $table = INDIVIDUALS;
    
    $sql = SQL_SELECT_COUNT."$database.$table WHERE YEAR(dday) = '".$year."' AND DATEDIFF(dday, bday) < '365'";
   
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
 
    $sql = SQL_SELECT_FROM."$database.$table WHERE year = $year";
    
    //LOGTEXT("SQL-clause : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
    $statistics = $query->fetchAll();

    //LOGARRAY($statistics);
    
    return $statistics;
}

function get_children_count() {
    
    $conn = connect_to_database();
    
    $database = DB;
    $table = FAMILIES;
    
    $sql = "SELECT child,xref FROM $database.$table";
    
    LOGTEXT("SQL-clause : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
    $children = $query->fetchAll();
    
    LOGARRAY($children);
    
    return $children;
}

function set_children_count($child_array) {
    
    LOGTEXT("SET_CHILDREN_COUNT : Asetetaan lasten määrä.");
    
    $conn = connect_to_database();
    
    $database = DB;
    $table = MARRIAGES;
    
    $sql = "INSERT INTO $database.$table (xref,childcount) values ('$child_array[0]',$child_array[1])";
    
    LOGTEXT("SQL-clause : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
}

function get_marriages() {
    
    LOGTEXT("GET_MARRIAGES : Haetaan avioliitot.");
    
    $conn = connect_to_database();
    
    $database = DB;
    $family_table = FAMILIES;
    $individual_table = INDIVIDUALS;
    
    //$index_family = "index_".$family_table;
    //$index_individual = "index_".$individual_table;
    
    // Tämä SQL-koodi vaikuttaa liian raskaalta minun tietokannan koolla ~200000 tietuetta
    $sql = "SELECT f.xref, hi.givn, hi.surn, hi.bday, hi.bplace, hi.dday, hi.dplace, hi.isdead, wi.givn, wi.surn, wi.bday, wi.bplace, wi.dday, wi.dplace, wi.isdead, f.marday, f.marplace ";
    $sql .= "FROM $database.$family_table f ";
    $sql .= "LEFT JOIN $database.$individual_table hi ON hi.xref = husb ";
    $sql .= "LEFT JOIN $database.$individual_table wi ON wi.xref = wife ";
    
    LOGTEXT("SQL-clause : ".$sql);
    
    $query = $conn->prepare($sql);
    $query->execute();
    $marriages = $query->fetchAll();
    
    LOGARRAY($marriages);
    
    return $marriages;
}

function set_marriage($marriage) {
    
    $conn = connect_to_database();
    
    $database = DB;
    $table = MARRIAGES; 

    $sql = "UPDATE $database.$table ";
    $sql .= "SET husbgivn= :husbgivn ";
    $sql .= ", husbsurn= :husbsurn ";
    $sql .= ", husbbday= :husbbday ";
    $sql .= ", husbbplace= :husbbplace ";
    $sql .= ", husbdday= :husbdday ";
    $sql .= ", husbdplace= :husbdplace ";
    $sql .= ", husbisdead= :husbisdead ";
    $sql .= ", wifegivn= :wifegivn ";
    $sql .= ", wifesurn= :wifesurn ";
    $sql .= ", wifebday= :wifebday ";
    $sql .= ", wifebplace= :wifebplace ";
    $sql .= ", wifedday= :wifedday ";
    $sql .= ", wifedplace= :wifedplace ";
    $sql .= ", wifeisdead= :wifeisdead ";
    $sql .= ", marday= :marday ";
    $sql .= ", marplace=:marplace ";
    $sql .= "WHERE xref= :xref";
    
    $query = $conn->prepare($sql);
    
    $query->bindValue(':xref',$marriage[0]);
    $query->bindValue(':husbgivn',$marriage[1]);
    $query->bindValue(':husbsurn',$marriage[2]);
    $query->bindValue(':husbbday',$marriage[3]);
    $query->bindValue(':husbbplace',$marriage[4]);
    $query->bindValue(':husbdday',$marriage[5]);
    $query->bindValue(':husbdplace',$marriage[6]);
    $query->bindValue(':husbisdead',$marriage[7]);
    $query->bindValue(':wifegivn',$marriage[8]);
    $query->bindValue(':wifesurn',$marriage[9]);
    $query->bindValue(':wifebday',$marriage[10]);
    $query->bindValue(':wifebplace',$marriage[11]);
    $query->bindValue(':wifedday',$marriage[12]);
    $query->bindValue(':wifedplace',$marriage[13]);
    $query->bindValue(':wifeisdead',$marriage[14]);
    $query->bindValue(':marday',$marriage[15]);
    $query->bindValue(':marplace',$marriage[16]);
    
    LOGTEXT("SQL-clause : ".$sql);
    
    $query->execute();
}

//
// Hakee tietokannasta avioliitot
//
function fetch_marriage_database($query, $limit, $offset) {
    
    LOGTEXT("FETCH_MARRIAGE_DATABASE : Haetaan henkilötietokantatiedot : ".$offset." - ".$offset+$limit);
    
    $connect = connect_to_database();
    
    $database = DB;
    $table = MARRIAGES;
    
    if(!isset($_SESSION["user_id"])) {
        
        LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan vain kuolleiden henkilötietokantatiedot (Ei olla kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = SQL_SELECT_FROM."$database.$table WHERE husbisdead='1' AND wifeisdead='1' AND ($query) LIMIT $limit OFFSET $offset";
            
        } else {
            
            $sql = SQL_SELECT_FROM."$database.$table WHERE husbisdead='1' AND wifeisdead='1' LIMIT $limit OFFSET $offset";
            
        }
    } else if(isset($_SESSION["user_id"])) {
        
        LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Haetaan kaikkien henkilötietokantatiedot (Ollaan kirjauduttu sisälle).");
        
        if ($query) {
            
            $sql = SQL_SELECT_FROM."$database.$table WHERE $query LIMIT $limit OFFSET $offset";
            
        } else {
            
            $sql = SQL_SELECT_FROM."$database.$table LIMIT $limit OFFSET $offset";
            
        }
    }
    
    LOGTEXT("FETCH_INDIVIDUAL_DATABASE : Kysely on : ".$sql);
    
    $query = $connect->prepare($sql);
    $query->execute();
    $array = $query->fetchAll();
    
    return $array;
}

?>