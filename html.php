<?php

require_once("convert_day.php");

//
// Tulostaa HTML-sivun alkuosan valitun otsikon kanssa.
//
function create_html_start($caption) {
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"";
    echo "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
    echo "<html>\n";
    echo "<head>\n";
    echo "  <meta http-equiv=\"Content-Type\"
      content=\"text/html;charset=utf-8\" />\n";
    echo "  <link rel=\"stylesheet\" type=\"text/css\"
      href=\"sukuverkko.css\" />\n";
    echo "  <title>".$caption."</title>\n";
    echo "</head>\n";
    echo "<body>\n\n";
}

//
// Tulostaa HTML-sivun loppuosan.
//
function create_html_end() {
    echo "</body>\n";
    echo "</html>\n";
}

//
// Uudelleenohjaa käyttäjän toiselle sivulle.
//
function redirect($page) {
    
    header("Location: http://" . $_SERVER["HTTP_HOST"]
        . dirname(htmlspecialchars($_SERVER["PHP_SELF"]))
        . "/" . $page);
}

//
// Tulostaa otsikkopaneelin
//
function create_html_header_panel() {
    
    echo "Tervetuloa Markon sukututkimusverkon sivustolle.<br>\n";
    echo "Täällä pystyy tekemään erilaisia hakuja minun tietokannasta (kuolleiden osalta). Elävien tietoihin pääsy vaatii kirjautumisen.<br>\n";
    echo "Lisäksi kannasta saa tilastotietoa eri tekijöistä (kaikista).<br>\n";
}

//
// Tulostaa pääsivun
//
function create_html_main_button_panel() {
    
    echo "<form action=\"main.php\" method=\"post\">\n";
    echo " <input type=\"submit\" name=\"a\" value=\"A\">\n";

    echo " <input type=\"submit\" name=\"statistics\" value=\"Tilastotietoa (kaikki)\">\n";
    
    if(!isset($_SESSION["user_id"])) {
        
        echo " <input type=\"submit\" name=\"get_marriage\" value=\"Hae avioliitot (molemmat kuolleet)\">\n";
        echo " <input type=\"submit\" name=\"search_individual\" value=\"Etsi henkilö (vain kuolleet)\">\n";
        echo " <input type=\"submit\" name=\"login_credit\" value=\"Kirjaudu sisälle\">\n";
        
    } else if(isset($_SESSION["user_id"])) {
        
        echo " <input type=\"submit\" name=\"get_marriage\" value=\"Hae avioliitot (kaikki)\">\n";
        echo " <input type=\"submit\" name=\"search_individual\" value=\"Etsi henkilö (kaikki)\">\n";
        echo " <input type=\"submit\" name=\"upload_gedcom\" value=\"Tuo gedcom\">\n";
        echo " <input type=\"submit\" name=\"create_statistics\" value=\"Luo tilastotietokanta\">\n";
        echo " <input type=\"submit\" name=\"create_marriages\" value=\"Luo avioliittotietokanta\">\n";
        echo " <input type=\"submit\" name=\"logout_credit\" value=\"Kirjaudu ulos\">\n";
    }

    echo "</form>\n";
}

//
// Tulostaa lukemapaneelin
//
function create_html_count_panel($individual_count, $family_count) {
    
    echo "<div class=\"count\">Tietokannassa on tällä hetkellä ".$individual_count." henkilöä ja ".$family_count." perhettä</div><br>\n";
}

//
// Tulostaa kirjautumissivun
//
function create_html_login() {
    
    echo " <form class=\"modal-content animate\" action=\"login.php\" method=\"post\">\n";
    echo "  <div class=\"container\">\n";
    echo "   <label for=\"label\"><b>Kirjautuminen sukuverkkoon:</b></label><br><br>\n";
    echo "   <label for=\"username\"><b>Admintunnus</b></label>\n";
    echo "   <input type=\"text\" name=\"username\" required>\n";
    echo "   <label for=\"password\"><b>Adminsalasana</b></label>\n";
    echo "   <input type=\"password\" name=\"password\" required>\n";
    echo "   <button class=\"button\" type=\"submit\" name=\"login\">Kirjaudu</button>\n";
    echo "  </div>\n";
    echo " </form>\n";
}

//
// Tulostaa tietojen lataussivun
//
function create_html_upload() {
    
    echo "<form class=\"modal-content animate\" action=\"upload.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
    echo " <div class=\"container\">\n";
    echo "  <label for=\"gedcom_data\"><b>Ladattava Gedcom-tiedosto : </b></label>\n";
    echo "  <input type=\"file\" name=\"gedcom_data\" id=\"gedcom_data\">\n";
    echo "  <button class=\"button\" type=\"submit\" name=\"upload_gedcom_data\">Lataa tiedostot</button>\n";
    echo "  <button class=\"button\" type=\"button\" onclick=\"window.location.href='main.php'\">Palaa takaisin</button>\n";
    echo " </div>\n";
    echo "</form>\n";   
}

//
// Tulostaa otsikkopaneelin
//
function create_html_individual_search_help_banner() {
    
    echo "Haku toimii päivämäärien osalta siten että voi joko hakea tarkalla päivämääräällä tai vuoden tarkkuudella.<br>\n";
    echo "Lisättäessa - merkki eteen haetaan kaikki tiedot ennen päivämäärää (-1972 tai -5.5.1972).<br>\n";
    echo "Lisättäessa - merkki taakse haetaan kaikki tiedot päivämäärän jälkeen (1972- tai 5.5.1972-).<br>\n";
    echo "Lisättäessa - merkki päivämäärien väliin haetaan kaikki tiedot päivämäärien välillä (1972-1973 tai 5.5.1972-5.5.1973).<br>\n";
}

//
// Tulostaa pääsivun
//
function create_html_individual_search_button_panel() {
    
    echo "<form action=\"search_individual.php\" method=\"post\">\n";
    echo " <input type=\"submit\" name=\"d\" value=\"D\">\n";
    echo " <input type=\"submit\" name=\"e\" value=\"E\">\n";
    echo " <input type=\"submit\" name=\"f\" value=\"F\">\n";
    echo " <input type=\"submit\" name=\"logout_search\" value=\"Palaa takaisin\">\n";
    echo "</form>\n";
}

//
// Tulostaa historiapaneelin
//
function create_history() {
    
    echo "5.5.2024 Lisättiin haku avioliiton mukaan<br><br>\n";
}

//
// Tulostaa copyrightpaneelin
//
function create_copyrigth() {
    
    echo "(c) 2024 Marko Alaluusua<br>\n";
}

//
// Tulostaa hakupaneelin
//
function create_html_individual_search_constraints_panel() {
    
    echo "<div class=\"row\">";
    echo "  <form action=\"search_individual.php\" method=\"post\">\n";
    echo "  <div class=\"column_individual\">";
    echo "      <label for=\"givn\">Rajaus etunimellä : </label><br>";
    echo "      <input type=\"text\" id=\"givn\" name=\"givn\" value=\"".$_SESSION["givn"]."\"><br>";
    echo "      <label for=\"surn\">Rajaus sukunimellä : </label><br>";
    echo "      <input type=\"text\" id=\"surn\" name=\"surn\" value=\"".$_SESSION["surn"]."\"><br>";
    echo "  </div>\n";
    
    echo "  <div class=\"column_individual\">";
    echo "      <label for=\"bday\">Rajaus syntymäpäivällä : </label><br>";
    echo "      <input type=\"text\" id=\"bday\" name=\"bday\" value=\"".$_SESSION["bday"]."\"><br>";
    echo "      <label for=\"bplace\">Rajaus syntymäpaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"bplace\" name=\"bplace\" value=\"".$_SESSION["bplace"]."\"><br>";
    echo "  </div>\n";
    
    echo "  <div class=\"column_individual\">";
    echo "      <label for=\"dday\">Rajaus kuolinpäivällä : </label><br>";
    echo "      <input type=\"text\" id=\"dday\" name=\"dday\" value=\"".$_SESSION["dday"]."\"><br>";
    echo "      <label for=\"dplace\">Rajaus kuolinpaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"dplace\" name=\"dplace\" value=\"".$_SESSION["dplace"]."\"><br>";
    echo "  </div>\n";
    
    echo "  <div class=\"column_individual\">";
    echo "      <label for=\"dcause\">Rajaus kuolinsyyllä : </label><br>";
    echo "      <input type=\"text\" id=\"dcause\" name=\"dcause\" value=\"".$_SESSION["dcause"]."\"><br>";
    echo "      <label for=\"occu\">Rajaus ammatilla : </label><br>";
    echo "      <input type=\"text\" id=\"occu\" name=\"occu\" value=\"".$_SESSION["occu"]."\"><br>";
    echo "  </div>\n";
    
    echo "  <div class=\"column_individual\">";
    echo "      <label for=\"buday\">Rajaus hautauspäivällä : </label><br>";
    echo "      <input type=\"text\" id=\"buday\" name=\"buday\" value=\"".$_SESSION["buday"]."\"><br>";
    echo "      <label for=\"buplace\">Rajaus hautauspaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"buplace\" name=\"buplace\" value=\"".$_SESSION["buplace"]."\"><br>";
    echo "  </div>\n";
    
    echo "  <div class=\"column_individual\">";
    echo "      <label for=\"chrday\">Rajaus ristimäpäivällä : </label><br>";
    echo "      <input type=\"text\" id=\"chrday\" name=\"chrday\" value=\"".$_SESSION["chrday"]."\"><br>";
    echo "      <label for=\"chrplace\">Rajaus ristimäpaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"chrplace\" name=\"chrplace\" value=\"".$_SESSION["chrplace"]."\"><br>";
    echo "  </div>\n";
    
    echo "  <div class=\"column_individual\">";
    echo "      <label for=\"moveplace\">Rajaus muuttopaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"move\" name=\"move\" value=\"".$_SESSION["move"]."\"><br>";
    echo "  </div>\n";
    
    echo "  <div class=\"column_individual\">";
    echo "      <button class=\"button\" type=\"submit\" name=\"individual_constraints_search\">Hae rajauksella</button>\n";
    echo "      <button class=\"button\" type=\"submit\" name=\"individual_search\">Hae yleisesti</button><br>\n";
    echo "      <label for=\"individual\">Hae yleisesti:</label>";
    echo "      <input type=\"text\" id=\"individual\" name=\"individual\" value=\"".$_SESSION["individual_name"]."\"><br>";
    echo "  </div>\n";
    
    echo "  </form>\n";
    echo "  </div>\n";
    echo "</div>";
}

//
// Tulostaa haun lukemapaneelin
//
function §($individual_count) {
    echo "<div class=\"count\">Haussa löytyi ".$individual_count." henkilöä</div><br>\n";
}

//
// Tulostaa henkilötietopaneelin
//
function create_html_individual_data_panel($pages, $pagenumber, $array) {
    
    echo "<div class=\"pages\" id=\"pages\">\n";
    for($page = 0; $page < $pages; ++$page)
    {
        if($pagenumber == $page)
            echo $page + 1, " ";
            else
                echo "<a href=\"search_individual.php?page=".$page."\">",
                $page + 1, "</a> ";
    }
    echo "</div>\n";
    
    echo "<table border='1'>\n";
    
    echo "  <tr>\n";
    echo "      <td>Etunimi</td>\n";
    echo "      <td>Sukunimi</td>\n";
    echo "      <td>Ammatti</td>\n";
    echo "      <td>Syntymäpäivä</td>\n";
    echo "      <td>Syntymäpaikka</td>\n";
    echo "      <td>Kuolinpäivä</td>\n";
    echo "      <td>Kuolinpaikka</td>\n";
    echo "      <td>Kuolinsyy</td>\n";
    echo "      <td>Hautauspäivä</td>\n";
    echo "      <td>Hautauspaikka</td>\n";
    echo "      <td>Ristiäispäivä</td>\n";
    echo "      <td>Ristiäispaikka</td>\n";
    echo "      <td>Muutto</td>\n";
    echo "      <td>Lähde</td>\n";
    echo "      <td>Muistiinpano</td>\n";
    echo "  </tr>\n";
    
    foreach ($array as $individual) {
        
        echo "      <tr>\n";
        echo "          <td>".$individual['givn']."</td>\n";
        echo "          <td>".$individual['surn']."</td>\n";
        echo "          <td>".$individual['occu']."</td>\n";
        echo "          <td>".modify_record_date_to_day($individual['bday'])."</td>\n\n";
        echo "          <td>".$individual['bplace']."</td>\n";
        echo "          <td>".modify_record_date_to_day($individual['dday'])."</td>\n";
        echo "          <td>".$individual['dplace']."</td>\n";
        echo "          <td>".$individual['dcause']."</td>\n";
        echo "          <td>".modify_record_date_to_day($individual['buday'])."</td>\n";
        echo "          <td>".$individual['buplace']."</td>\n";
        echo "          <td>".modify_record_date_to_day($individual['chrday'])."</td>\n";
        echo "          <td>".$individual['chrplace']."</td>\n";
        echo "          <td>".$individual['move']."</td>\n";
        echo "          <td>".$individual['source']."</td>\n";
        echo "          <td>".$individual['note']."</td>\n";
        echo "  </tr>\n";
    }
    
    echo "</table>\n";
}

//
// Tulostaa haun lukemapaneelin
//
function create_html_search_count_panel($individual_count) {
    
    echo "<div class=\"count\">Haussa löytyi ".$individual_count." henkilöä</div><br>\n";
}

//
// Tulostaa statistiikkasivun yläpaneelin
//
function create_html_statistics_button_panel() {
    
    echo "<form action=\"statistics.php\" method=\"post\">\n";
    echo " <input type=\"submit\" name=\"g\" value=\"G\">\n";
    echo " <input type=\"submit\" name=\"h\" value=\"H\">\n";
    echo " <input type=\"submit\" name=\"time_statistics\" value=\"Elinaikatilastoja\">\n";
    echo " <input type=\"submit\" name=\"logout_statistics\" value=\"Palaa takaisin\">\n";
    echo "</form>\n";
}

//
// Tulostaa statistiikkasivun lukemapaneelin
//
function create_html_statistics_count_panel($individual_count) {
    
    echo "<div class=\"count\">Tietokannassa on tällä hetkellä ".$individual_count." henkilöä</div><br>\n";
}

//
// Tulostaa statistiikkasivun tietopaneelin
//
function create_html_statistics_panel() {
    
    echo "</div>\n";
    
    echo "<table border='1'>\n";
    
    echo "  <tr>\n";
    echo "      <td>Vuosi</td>\n";
    echo "      <td>Syntyneet</td>\n";
    echo "      <td>Kuolleet</td>\n";
    echo "      <td>Vihityt</td>\n";
    echo "      <td>Lapsikuolleisuus 0-1 v</td>\n";
    echo "  </tr>\n";
    
    for($year = 1700;$year<=date("Y");$year++) {
        
        echo "      <tr>\n";
        echo "          <td>".$year."</td>\n";
        $statisticsarray = get_statistics_by_year($year);
        foreach ($statisticsarray as $statistics) {
            echo "          <td>".$statistics[1]."</td>\n";
            echo "          <td>".$statistics[2]."</td>\n";
            echo "          <td>".$statistics[3]."</td>\n";
            echo "          <td>".$statistics[4]."</td>\n";
        }
        echo "  </tr>\n";
    }
}

//
// Tulostaa avioliiton hakusivun
//
function create_html_marriage_search_button_panel() {
    
    echo "<form action=\"search_marriage.php\" method=\"post\">\n";
    echo " <input type=\"submit\" name=\"d\" value=\"D\">\n";
    echo " <input type=\"submit\" name=\"e\" value=\"E\">\n";
    echo " <input type=\"submit\" name=\"f\" value=\"F\">\n";
    echo " <input type=\"submit\" name=\"logout_marriage\" value=\"Palaa takaisin\">\n";
    echo "</form>\n";
}

//
// Tulostaa hakupaneelin
//
function create_html_marriage_search_constraints_panel() {
    
    echo "<div class=\"row\">";
    echo "  <form action=\"search_marriage.php\" method=\"post\">\n";
    echo "  <div class=\"column_marriage\">";
    echo "      <label for=\"givn\">Rajaus miehen etunimellä : </label><br>";
    echo "      <input type=\"text\" id=\"husbgivn\" name=\"husbgivn\" value=\"".$_SESSION["husbgivn"]."\"><br>";
    echo "      <label for=\"surn\">Rajaus miehen sukunimellä : </label><br>";
    echo "      <input type=\"text\" id=\"husbsurn\" name=\"husbsurn\" value=\"".$_SESSION["husbsurn"]."\"><br>";
    echo "  </div>\n";
    
    echo "  <div class=\"column_marriage\">";
    //echo "      <label for=\"bday\">Rajaus miehen syntymäpäivällä : </label><br>";
    //echo "      <input type=\"text\" id=\"husbbday\" name=\"husbbday\" value=\"".$_SESSION["husbbday"]."\"><br>";
    echo "      <label for=\"bplace\">Rajaus miehen syntymäpaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"husbbplace\" name=\"husbbplace\" value=\"".$_SESSION["husbbplace"]."\"><br>";
    //echo "      <label for=\"bday\">Rajaus miehen kuolinpäivällä : </label><br>";
    //echo "      <input type=\"text\" id=\"husbdday\" name=\"husbdday\" value=\"".$_SESSION["husbdday"]."\"><br>";
    echo "      <label for=\"bplace\">Rajaus miehen kuolinpaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"husbdplace\" name=\"husbdplace\" value=\"".$_SESSION["husbdplace"]."\"><br>";
    echo "  </div>\n";
       
    echo "  <div class=\"column_marriage\">";
    echo "      <label for=\"givn\">Rajaus vaimon etunimellä : </label><br>";
    echo "      <input type=\"text\" id=\"wifegivn\" name=\"wifegivn\" value=\"".$_SESSION["wifegivn"]."\"><br>";
    echo "      <label for=\"surn\">Rajaus vaimon sukunimellä : </label><br>";
    echo "      <input type=\"text\" id=\"wifesurn\" name=\"wifesurn\" value=\"".$_SESSION["wifesurn"]."\"><br>";
    echo "  </div>\n";
       
    echo "  <div class=\"column_marriage\">";
    //echo "      <label for=\"bday\">Rajaus vaimon syntymäpäivällä : </label><br>";
    //echo "      <input type=\"text\" id=\"wifebday\" name=\"wifebday\" value=\"".$_SESSION["wifebday"]."\"><br>";
    echo "      <label for=\"bplace\">Rajaus vaimon syntymäpaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"wifebplace\" name=\"wifebplace\" value=\"".$_SESSION["wifebplace"]."\"><br>";
    //echo "      <label for=\"bday\">Rajaus vaimon kuolinpäivällä : </label><br>";
    //echo "      <input type=\"text\" id=\"wifedday\" name=\"wifedday\" value=\"".$_SESSION["wifedday"]."\"><br>";
    echo "      <label for=\"bplace\">Rajaus vaimon kuolinpaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"wifedplace\" name=\"wifedplace\" value=\"".$_SESSION["wifedplace"]."\"><br>";
    echo "  </div>\n";

    echo "  <div class=\"column_marriage\">";
    echo "      <label for=\"buday\">Rajaus vihkimispäivällä : </label><br>";
    echo "      <input type=\"text\" id=\"marday\" name=\"marday\" value=\"".$_SESSION["marday"]."\"><br>";
    echo "      <label for=\"buplace\">Rajaus vihkimispaikalla : </label><br>";
    echo "      <input type=\"text\" id=\"marplace\" name=\"marplace\" value=\"".$_SESSION["marplace"]."\"><br>";
    echo "  </div>\n";
 
    echo "  <div class=\"column_individual\">";
    echo "      <button class=\"button\" type=\"submit\" name=\"marriage_search\">Hae rajauksella</button>\n";
    echo "  </div>\n";
    
    
    echo "  </form>\n";
    echo "  </div>\n";
    echo "</div>";
}

//
// Tulostaa avioliittotietopaneelin
//
function create_html_marriage_data_panel($pages, $pagenumber, $array) {
    
    echo "<div class=\"pages\" id=\"pages\">\n";
    for($page = 0; $page < $pages; ++$page)
    {
        if($pagenumber == $page)
            echo $page + 1, " ";
            else
                echo "<a href=\"search_marriage.php?page=".$page."\">",
                $page + 1, "</a> ";
    }
    echo "</div>\n";
    
    echo "<table border='1'>\n";
    
    echo "  <tr>\n";
    echo "      <td>Miehen etunimi</td>\n";
    echo "      <td>Miehen sukunimi</td>\n";
    echo "      <td>Miehen syntymäpäivä</td>\n";
    echo "      <td>Miehen syntymäpaikka</td>\n";
    echo "      <td>Miehen kuolinpäivä</td>\n";
    echo "      <td>Miehen kuolinpaikka</td>\n";
    echo "      <td>Vaimon etunimi</td>\n";
    echo "      <td>Vaimon sukunimi</td>\n";
    echo "      <td>Vaimon syntymäpäivä</td>\n";
    echo "      <td>Vaimon syntymäpaikka</td>\n";
    echo "      <td>Vaimon kuolinpäivä</td>\n";
    echo "      <td>Vaimon kuolinpaikka</td>\n";
    echo "      <td>Avioitusmispäivä</td>\n";
    echo "      <td>Avioitusmispaikka</td>\n";
    echo "      <td>Lasten lukumäärä</td>\n";
    echo "  </tr>\n";
    
    foreach ($array as $marriage) {
        
        echo "      <tr>\n";
        echo "          <td>".$marriage['husbgivn']."</td>\n";
        echo "          <td>".$marriage['husbsurn']."</td>\n";
        echo "          <td>".modify_record_date_to_day($marriage['husbbday'])."</td>\n\n";
        echo "          <td>".$marriage['husbbplace']."</td>\n";
        echo "          <td>".modify_record_date_to_day($marriage['husbdday'])."</td>\n";
        echo "          <td>".$marriage['husbdplace']."</td>\n";
        echo "          <td>".$marriage['wifegivn']."</td>\n";
        echo "          <td>".$marriage['wifesurn']."</td>\n";
        echo "          <td>".modify_record_date_to_day($marriage['wifebday'])."</td>\n\n";
        echo "          <td>".$marriage['wifebplace']."</td>\n";
        echo "          <td>".modify_record_date_to_day($marriage['wifedday'])."</td>\n";
        echo "          <td>".$marriage['wifedplace']."</td>\n";
        echo "          <td>".modify_record_date_to_day($marriage['marday'])."</td>\n";
        echo "          <td>".$marriage['marplace']."</td>\n";
        echo "          <td>".$marriage['childcount']."</td>\n";
        echo "  </tr>\n";
    }
    
    echo "</table>\n";
}

?>