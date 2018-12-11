<?php
require_once('constants.php');

$dir = '';//leave this empty as we don't know until run time what it is

//this is if it's just non gzipped sql file
//system("mysql -u " . DBUSER . " -p" . DBPASS  . " " . STORE_UPDATES . " < " . DB_BU_DIR . "$dir/" . DB_BU_FILE);

system("gunzip < " . DB_BU_DIR . "$dir/" . DB_BU_FILE . ".gz | mysql -u " . DBUSER . " -p" . DBPASS  . " " . STORE_UPDATES);