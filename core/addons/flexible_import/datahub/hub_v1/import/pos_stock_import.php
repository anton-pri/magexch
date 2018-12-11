<?php
require_once('header.php');
if(empty($dwb_link)) {
	die('problem with the script');
}
pos_stock::import_and_update();
$sql = "SELECT * FROM " . pos_stock::table_name();
$result = mysql_query($sql, $dwb_link) or sql_error($sql); 	

$swe_link = mysql_connect(SWE_HOST, SWE_DBUSER, SWE_DBPASS) or die('Could not connect to db');
mysql_select_db(SWE_XCART_DB, $swe_link) or die('Problem with database');
	
$sql = "DELETE FROM " . pos_stock::table_name();
mysql_query($sql, $swe_link) or sql_error($sql);
		

		
		
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) { 
	$string = '';
	foreach ($row as $k => $v) {
		$v = mysql_real_escape_string($v);
		$string .= "'$v',";
	}
	$string = rtrim($string, ',');
	$sql = "INSERT INTO  " . pos_stock::table_name() . " VALUES ($string)";

	$result2 = mysql_query($sql, $swe_link) or sql_error($sql); 			   
}

echo "<h2>The pos_stock table has been repopulated</h2>";
