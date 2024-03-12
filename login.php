<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("sql.php");
require_once("logging.php");
require_once("html.php");



if(isset($_POST["login"])) {
    
    login();
    exit;
}

create_html_start("Sukuverkko sisäänkirjautuminen");

create_html_login();

create_html_end();

function login() {   
    
    LOGTEXT("LOGIN : Aloitetaan kirjautuminen");
    
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    
    LOGTEXT("LOGIN : Admin tunnus : ".$username);
    LOGTEXT("LOGIN : Admin salasana : ".$password);
    
    //$password = sha1( $password );
    
    $connection = connect_to_database();
    
    $user_id = 0;
    
    try {
        
        $user = array($username,$password);
        
        $sql = "SELECT user_id, username, password FROM accounts WHERE username = ? AND password = ?";
        
        LOGTEXT("LOGIN : Luodaan kysely : SELECT user_id, username, password FROM accounts WHERE username = ".$username." AND password = ".$password);
        
        $query = $connection->prepare($sql);
        
        $query->execute($user);
        
        $user_id = $query->fetchColumn();
        
    } catch(Exception $e) {
        
        /*** if we are here, something has gone wrong with the database ***/
        echo 'We are unable to process your request. Please try again later';
    }
    
    if($user_id == 0) {
        
        LOGTEXT("LOGIN : Kirjautuminen epäonnistui");
        LOGTEXT("LOGIN : Palaa <a href=\"main.php\">pääsivulle</a>.");
        
        if (!GET_LOGGING())
            redirect('main.php');
            
    } else {
        
        session_start();
        
        // Otetaan talteen käyttäjän user id
        $_SESSION["user_id"] = $user_id;
        
        LOGTEXT("LOGIN : Kirjautuminen onnistui");
        LOGTEXT("LOGIN : Käyttäjä ID : ".$_SESSION["user_id"]);
        LOGTEXT("LOGIN : Jatka <a href=\"main.php\">pääsivulle</a>.");
        
        if (!GET_LOGGING())
            redirect('main.php');
    }
}

?>