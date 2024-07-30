<?php

define("HOST", "mysql:host=db1.n.kapsi.fi;dbname=magetsu");
define("USERNAME", "magetsu");
define("PASSWORD", "FgQiYWtkRX");
define("DB", "magetsu");
define("DATA_DELIMITER", "¤");
define("MOVEDAY_FAMILYCARD","move_family_card");
define("MOVEDAY_INDIVIDUAL","move_individual");
define("FALLEN_SOLDIER","Kaatui sodassa");

// Tuotantotietokanta

define("FAMILIES", "familynet_families");
define("INDIVIDUALS", "familynet_individuals");
define("SOURCES", "familynet_sources");
define("STATISTICS", "familynet_statistics");
define("MARRIAGES", "familynet_marriages");

//  Testaustietokanta
/*
define("FAMILIES", "familynet_families_v2");
define("INDIVIDUALS", "familynet_individuals_v2");
define("SOURCES", "familynet_sources_v2");
define("STATISTICS", "familynet_statistics_v2");
define("MARRIAGES", "familynet_marriages_v2");
*/

define ("RESULTS_IN_PAGE", 1000);

// SQL-komennot

define("SQL_SHOW_TABLES","SHOW TABLES");
define("SQL_DROP_TABLE","DROP TABLE IF EXISTS ");
define("SQL_CREATE_TABLE","CREATE TABLE IF NOT EXISTS ");
define("SQL_SELECT_COUNT","SELECT COUNT(*) FROM ");
define("SQL_SELECT_FROM","SELECT * FROM ");

// INSERT-komennot
define("SQL_INSERT_INTO","INSERT INTO ");
define("SQL_INSERT_INDIVIDUAL"," (xref,givn,surn,sex,occu,bday,bplace,dday,dplace,dcause,buday,buplace,chrday,chrplace,note,move,isdead,source,famc, fams) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
define("SQL_INSERT_FAMILY"," (xref,husb,wife,marday,marplace,child) values ( ?, ?, ?, ?, ?, ?)");
define("SQL_INSERT_SOURCE"," (xref,name) values ( ?, ?)");

// INDEX-komennot
define("SQL_CREATE_INDEX","CREATE INDEX ");
define("SQL_FAMILY_INDEX","(xref, husb, wife, marday, marplace)");
define("SQL_INDIVIDUAL_INDEX","(xref, givn, bday, bplace, dday, dplace, isdead)");

// SQL-tietueet

define("SQL_FAMILY_TABLE","( xref VARCHAR(10), 
                             husb VARCHAR(10), 
                             wife VARCHAR(10), 
                             marday DATE, 
                             marplace VARCHAR(100), 
                             child VARCHAR(1000) )");

define("SQL_INDIVIDUAL_TABLE"," ( xref VARCHAR(10),
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
                				  source VARCHAR(100),
                                  famc VARCHAR(10),
                                  fams VARCHAR(100))");

define("SQL_SOURCE_TABLE"," ( xref VARCHAR(10), name VARCHAR(100) )");

define("SQL_STATISTICS_TABLE"," ( year VARCHAR(10),
                                  bircount VARCHAR(10),
								  marcount VARCHAR(10),
                                  detcount VARCHAR(10),
                                  infdcount VARCHAR(10) )");

define("SQL_MARRIAGE_TABLE"," ( xref VARCHAR(10),
                                husbgivn VARCHAR(100),
                				husbsurn VARCHAR(100),
                				husbbday DATE,
                				husbbplace VARCHAR(100),
                				husbdday DATE,
                				husbdplace VARCHAR(100),
                                husbisdead INT(1),
                                wifegivn VARCHAR(100),
                				wifesurn VARCHAR(100),
                				wifebday DATE,
                				wifebplace VARCHAR(100),
                		        wifedday DATE,
                				wifedplace VARCHAR(100),
                                wifeisdead INT(1),
                				marday DATE,
                				marplace VARCHAR(100),
                                childcount VARCHAR(10) )");
    
?>