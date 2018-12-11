<?php
//require './config.php';
//require './auth.php';

//include('header.php');
include('constants.php');
//$host = "10.177.69.44";
$user = "cartDBa";
$passw = "fhgJfH760Jsd";
//$db_live = "live";	
//$db_hub = "hub";
/**
 * @todo set servers to live servers
 */
/**
 * watch this!
 */     
//        mysql_close($dwb_link);
 
	$site_updates_table = 'site_updates';
	

	
	$sql = "SELECT * FROM xfer_products_DWB";
	$result = mysql_query($sql, $dwb_link) or die(mysql_error()); 	
	mysql_close($dwb_link);
	
	$dwe_link = mysql_connect('discountwinebuys.com', $user, $passw) or die('Could not connect to db');
	mysql_select_db('xcartDB', $dwe_link) or die('Problem with database');	
	
	$sql = "DELETE FROM $site_updates_table";
	$r = mysql_query($sql, $dwe_link) or die(mysql_error()); 	
	
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) { 
		$string = '';
		foreach ($row as $k => $v) {
			if($k == 'sku') {
				continue;
			}
			$v = mysql_real_escape_string($v);
			$string .= "'$v',";
		}
		$string = rtrim($string, ',');
		$sql = "INSERT INTO $site_updates_table VALUES ($string)";
	  $result2 = mysql_query($sql, $dwe_link) or die(mysql_error()); 	
	   
	} 	
    
echo "<h2>site_update tables has been repopulated for dwb</h2>";
?>
<a href="http://www.saratogawine.com/xc/mysql_access/import/">Home</a>
