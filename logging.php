<?php

define ("LOGGING", false);

function LOGTEXT($prompt) {
    
    if (LOGGING) {
        
        echo $prompt."<br>";
        
    } else return;    
}

function LOGARRAY($array) {
    
    if (LOGGING) {
        
        LOGTEXT("Taulukon tulos : <br>");
        echo var_dump($array)."<br>";
        LOGTEXT("--------------------------------------");
        
    } else return;
}

function GET_LOGGING() {

        return LOGGING;
}

?>