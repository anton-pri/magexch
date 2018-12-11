<?php

//require '../../config.php';
require './auth.php';

require './mysql_access/import/header.php';
//require_once './mysql_access/import/xfer_prune.php';
	//include('constants.php');
$host = "localhost";
$user = "saratoga_dbuser";
$passw = "vm5BTzB=4QXn";
$db_live = "saratoga_live_xcart";	
$db_hub = "saratoga_live_hub";
/**
 * @todo set servers to live servers
 */
/**
 * watch this!
 */     
	$hub_link = mysql_connect('localhost', $user, $passw) or die('Could not connect to db');
	mysql_select_db($db_hub, $hub_link) or die('Problem with database');
	
	
	$sql = "DELETE FROM xfer_products_SWE_snapshot";
	mysql_query($sql, $hub_link) or die(mysql_error()); 
	
	$sql = "Insert into `xfer_products_SWE_snapshot`\n"
    . "SELECT * FROM `xfer_products_SWE`";
	mysql_query($sql, $hub_link) or die(mysql_error()); 
	
	$sql = "SELECT * FROM xfer_products_SWE";
	$data = mysql_query($sql, $hub_link) or die(mysql_error()); 	
	mysql_close($hub_link);
	 
	$site_updates_table = 'site_updates';
	
	$live_link = mysql_connect('localhost', $user, $passw) or die('Could not connect to db');
	mysql_select_db($db_live, $live_link) or die('Problem with database');

	$sql = "DELETE FROM $site_updates_table";
	mysql_query($sql, $live_link) or die(mysql_error()); 

	while($row = mysql_fetch_array($data, MYSQL_ASSOC)) { 

		$string = array();
		foreach ($row as $k => $v) {
			$string[] = "'".mysql_real_escape_string($v)."'";
		}
		$string = implode(',',$string);
		$sql = "INSERT INTO $site_updates_table VALUES ($string)";
	  
	  mysql_query($sql, $live_link) or die(mysql_error());
          unset($string);
	   
	} 
	//store 2

echo "<h2>site_update tables have been repopulated on both sites</h2>";
