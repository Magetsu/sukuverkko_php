<?php

require_once("logging.php");
require_once("html.php");
require_once("modify_records.php");

/*
 * Luodaan perhekortti yksilöstä
 */
function create_familycard_from_individual($individualcard_id) {
    
    LOGTEXT("CREATE_FAMILYCARD_FROM_INDIVIDUAL : Yksilökortti on ".$individualcard_id);
    
    $children_line = "";
    
    // Haetaan yksilön kortti
    $individual_card = fetch_individual($individualcard_id);
   
    // Luodaan kortti henkilöstä joka ei koskaan mene naimisiin tai on kuollut nuorena
    if (!$individual_card[0]['fams']) {
        
        LOGTEXT("CREATE_FAMILYCARD_FROM_INDIVIDUAL : Ei koskaan mene naimisiin tai on kuollut nuorena. ");

        // Luodaan lapsen isä- ja äiti-rivit
        $father_line = create_parent_line($individual_card);
        $mother_line = "";
        
        
        $marriage_line = "";
        
        // Luodaan vanhempien lähteet
        $father_source = create_source($individual_card);
        $mother_source = "";
        
        // Luodaan vanhempien asutushistoria
        $father_history_line = create_history($individual_card);
        $mother_history_line = "";
/*
        // Haetaan kortti jossa henkilö on lapsena            
        $family_card = get_familycard($individual_card[0]['famc']);
        
        // Haetaan lapsen isä ja äiti
        $father = fetch_individual($family_card[0]['husb']);
        $mother = fetch_individual($family_card[0]['wife']);
        
        // Luodaan lapsen isä- ja äiti-rivit
        $father_line = create_parent_line($father);
        $mother_line = create_parent_line($mother);
        
        // Luodaan vanhempien avioliittorivi jos mainitaan aviopäivä tai -paikka      
        if($family_card[0]['marday'] OR $family_card[0]['marplace']) {
            
            $marriage_line = create_marriage_line(modify_record_date_to_day($family_card[0]['marday']), $family_card[0]['marplace']);
        }
        
        // Luodaan vanhempien lapsirivit jos on lapsia
        if ($family_card[0]['child']) {
                        
            $children_array = explode(DATA_DELIMITER, $family_card[0]['child'], -1);
            
            $count = 1;
            
            foreach ($children_array as $child_id) {
                
                $child = fetch_individual($child_id);
                
                $children_line .= create_children_line($count, $child);
                
                $count++;
            }
            
        } else if (!$family_card[0]['child']) {
            
            $children_line = "Ei lapsia";
        }

        // Luodaan vanhempien lähteet
        $father_source = create_source($father);
        $mother_source = create_source($mother);
        
        // Luodaan vanhempien asutushistoria
        $father_history_line = create_history($father);
        $mother_history_line = create_history($mother);
*/        
        $familycard = array();
        $familycard['father_line'] = $father_line;
        $familycard['mother_line'] = $mother_line;
        $familycard['marriage_line'] = $marriage_line;
        $familycard['children_line'] = $children_line;
        $familycard['father_source'] = $father_source;
        $familycard['mother_source'] = $mother_source;
        $familycard['father_history_line'] = $father_history_line;
        $familycard['mother_history_line'] = $mother_history_line;
   
        create_html_start("Perhekortti");
        create_html_family_card($familycard);
        create_html_end();
        
    // Luodaan kortti henkilöstä joka on mennyt naimisiin
    } else if ($individual_card[0]['fams']) {
        
        LOGTEXT("CREATE_FAMILYCARD_FROM_INDIVIDUAL : On mennyt naimisiin");

        $family_card_as_spouse = $individual_card[0]['fams'];
        
        $fams_array = explode(DATA_DELIMITER,$family_card_as_spouse,-1);
        
        LOGTEXT("CREATE_FAMILYCARD_FROM_INDIVIDUAL : Haetaan yksilön FAMS-kortti : ");
                                    
        compose_card($fams_array);
    }
}

/*
 * Luodaan perhekortti perheestä
 */
function create_familycard_from_family($card_id) {
    
    LOGTEXT("CREATE_FAMILYCARD_FROM_FAMILY : FAMC-kortti on ".$card_id);
    
    $family_array = get_familycard($card_id);
    
    LOGARRAY($family_array);
    
    $father_id = $family_array[0]['husb'];
    $mother_id = $family_array[0]['wife'];
   
    $father = fetch_individual($father_id);
    $mother = fetch_individual($mother_id);
    
    $father_fams = $father[0]['fams'];
    $mother_fams = $mother[0]['fams'];
    
    LOGARRAY($father);
    LOGARRAY($mother);
    
    $father_fams_array = explode(DATA_DELIMITER,$father_fams,-1);
    
    LOGTEXT("CREATE_FAMILYCARD_FROM_FAMILY : Haetaan isän FAMS-kortti : ");
    
    $mother_fams_array = explode(DATA_DELIMITER,$mother_fams,-1);
    
    LOGTEXT("CREATE_FAMILYCARD_FROM_FAMILY : Haetaan äidin FAMS-kortti : ");
    
    compose_card($father_fams_array);
    //compose_card($mother_fams_array);
}

function compose_card($fams_array) {
    
    LOGTEXT("COMPOSE_CARD : Luodaan perhekortti");
    
    foreach ($fams_array as $fams) {
        
        $children_line = "";
        
        $family_array = get_familycard($fams);
        
        $father_id = $family_array[0]['husb'];
        $mother_id = $family_array[0]['wife'];
        $marday = $family_array[0]['marday'];
        $marplace = $family_array[0]['marplace'];
        
        // Haetaan isä ja äiti
        $father = fetch_individual($father_id);
        $mother = fetch_individual($mother_id);
        
        // Luodaan isä- ja äiti-rivit
        $father_line = create_parent_line($father);
        $mother_line = create_parent_line($mother);
        
        // Luodaan avioliittorivi jos mainitaan aviopäivä tai -paikka
        if($marday OR $marplace) {
            
            $marriage_line = create_marriage_line(modify_record_date_to_day($marday), $marplace);
        }
        
        // Luodaan lapsirivit jos on lapsia
        if ($family_array[0]['child']) {
                       
            $children_array = explode(DATA_DELIMITER, $family_array[0]['child'], -1);
            
            $count = 1;
            
            foreach ($children_array as $child_id) {
                
                $child = fetch_individual($child_id);
                
                $children_line .= create_children_line($count, $child);
                
                $count++;
            }
            
        } else if (!$family_array[0]['child']) {
            
            $children_line = "Ei lapsia";
        }
                
        // Luodaan vanhempien lähteet
        $father_source = create_source($father);
        $mother_source = create_source($mother);
        
        // Luodaan vanhempien asutushistoria
        $father_history_line = create_history($father);
        $mother_history_line = create_history($mother);
        
        $familycard = array();
        $familycard['father_line'] = $father_line;
        $familycard['mother_line'] = $mother_line;
        $familycard['marriage_line'] = $marriage_line;
        $familycard['children_line'] = $children_line;
        $familycard['father_source'] = $father_source;
        $familycard['mother_source'] = $mother_source;
        $familycard['father_history_line'] = $father_history_line;
        $familycard['mother_history_line'] = $mother_history_line;
        
        create_html_start("Perhekortti");
        create_html_family_card($familycard);
        create_html_end();
    }
}

function create_parent_line($parent) {
    
    LOGTEXT("CREATE_PARENT_LINE : Luodaan vanhempi");

    $line ='';
    
    foreach ($parent as $data) {
        
        if ($data['famc']) {
            
            $line = "<b><a href=\"search_individual.php?familycard=".$data['famc']."\">".$data['givn']." ".$data['surn']."</a></b>, s. ".modify_record_date_to_day($data['bday'])." ".$data['bplace'];
        
        } elseif(!$data['famc']) {
            
            $line = "<b>".$data['givn']." ".$data['surn']."</b>, s. ".modify_record_date_to_day($data['bday'])." ".$data['bplace'];
        }
        
        if($data['isdead'] == '1') {
            
            $line .= ", k. ".modify_record_date_to_day($data['dday'])." ".$data['dplace'].".<br>";
            
        } else if($data['isdead'] == '0'){
            
            $line .= "<br>";
        }
        
        if ($data['occu']) {
            
            $line .= "Ammatti : ".$data['occu'].".<br>";
            
        }
        
        if ($data['dcause']) {
            
            $line .= "Kuolinsyy : ".$data['dcause'].".<br>";
            
        }
        
        if ($data['move']) {
            
            $line .= "Muutto : ".modify_move(MOVEDAY_FAMILYCARD,$data['move']).".<br>";
            
        } else {
            
            $line .= "<br>";
        }
        
        if ($data['famc']) {
            
            $family_array = get_familycard($data['famc']);

            $father_id = $family_array[0]['husb'];
            $mother_id = $family_array[0]['wife'];
            
            // Haetaan isä ja äiti
            $father = fetch_individual($father_id);
            $mother = fetch_individual($mother_id);
            
            // Luodaan isä- ja äiti-rivit
            foreach ($father as $data) {
                
                $father_line = "<b>".$data['givn']." ".$data['surn']."</b>, s. ".modify_record_date_to_day($data['bday'])." ".$data['bplace'];
                
                if($data['isdead'] == '1') {
                    
                    $father_line .= ", k. ".modify_record_date_to_day($data['dday'])." ".$data['dplace'].".<br>";
                    
                } else if($data['isdead'] == '0'){
                    
                    $father_line .= "<br>";
                }
            }
            
            $line .= "Isä  : ".$father_line;
            
            foreach ($mother as $data) {
                
                $mother_line = "<b>".$data['givn']." ".$data['surn']."</b>, s. ".modify_record_date_to_day($data['bday'])." ".$data['bplace'];
                
                if($data['isdead'] == '1') {
                    
                    $mother_line .= ", k. ".modify_record_date_to_day($data['dday'])." ".$data['dplace'].".<br>";
                    
                } else if($data['isdead'] == '0'){
                    
                    $mother_line .= "<br>";
                }
            }
            
            $line .= "Äiti : ".$mother_line;
        }
    }
    
    return $line;
}

function create_children_line($count, $child) {
    
    LOGTEXT("CREATE_CHILDREN_LINE : Luodaan lapsi");
    
    $line = '';
    
    foreach ($child as $data) {
                
        if ($data['fams']) {
            
            $line = "<tr><td colspan='1'>".$count.". <b><a href=\"search_individual.php?individualcard=".$data['xref']."\">".$data['givn']." ".$data['surn']."</a></b></td><td colspan='1'> s. ".modify_record_date_to_day($data['bday'])." ".$data['bplace']."</td>";
            
        } else if (!$data['fams']) {
            
            $line = "<tr><td colspan='1'>".$count.". <b><a href=\"search_individual.php?individualcard=".$data['xref']."\">".$data['givn']." ".$data['surn']."</a></b></td><td colspan='1'> s. ".modify_record_date_to_day($data['bday'])." ".$data['bplace']."</td>";     
        }
        
        if($data['isdead'] == '1') {
            
            $line .= "<td colspan='1'> k. ".modify_record_date_to_day($data['dday'])." ".$data['dplace']."</td>";
            
        } else if($data['isdead'] == '0'){
            
            $line .= "<td colspan='1'></td>";
        }

        if ($data['move']) {
            
            $line .= "<td colspan='1'><i> Muutto - ".modify_move(MOVEDAY_FAMILYCARD,$data['move']).".</i>"."</td></tr>";
            
        } else {
            
            $line .= "<br>";
        }
    }
    
    $line .= "\n";
    return $line;
}

function create_marriage_line($marday, $marplace) {
    
    LOGTEXT("CREATE_PARENT : Luodaan avioliitto");
    
    $line = '';
    
    if ($marday OR $marplace) {
        
        $line .= "Vihitty : ".$marday." ".$marplace." ";
    }
    return $line;
}

function create_source($individual) {
    
    LOGTEXT("CREATE_HISTORY : Luodaan lähteet");
    
    $line = '';
    
    foreach ($individual as $data) {
        
        LOGARRAY($data);
        
        if($data['source'] != '') {
            
            $source_array = explode(DATA_DELIMITER,$data['source'],-1);
            
            foreach ($source_array as $source) {
                
                if ($source != '') {
                    
                    $saucearray = get_source($source);
                    
                    $line .= $saucearray[0][0]."<br>";
                }
            }
        }
    }
    return $line;
}

function create_history($individual) {
    
    LOGTEXT("CREATE_HISTORY : Luodaan asutushistoria");
    
    $line = '';
    
    foreach ($individual as $data) {
        
        $line = $data['note'];
    }
    return $line;
}

?>