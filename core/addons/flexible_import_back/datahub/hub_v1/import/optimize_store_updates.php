<?php 
require_once('header.php');

$swe_link = mysql_connect(HOST, SWE_DBUSER, SWE_DBPASS) or die('Could not connect to db');
mysql_select_db(SWE_XCART_DB, $swe_link) or die('Problem with database');

$result = mysql_list_tables(STORE_UPDATES);

# Begin table optimizations.
for ($i = 0; $i < mysql_num_rows($result); $i++) {
	$current_table = mysql_tablename($result, $i);
	$sql = 'OPTIMIZE TABLE '.$current_table;
	$opt_table = mysql_query($sql,$swe_link) or sql_error($sql); 	
	
	if (!$opt_table && preg_match("/'(\S+)\.(MYI|MYD)/",mysql_error(), $damaged_table)) {
		# Repair broken indexes.
		$sql = 'REPAIR TABLE '.$damaged_table[1];		
 		mysql_query($sql,$swe_link) or sql_error($sql); 	
		$opt_table = null;
		# Try to optimize the table again.
		$sql = 'OPTIMIZE TABLE '.$current_table;
		$opt_table = mysql_query($sql,$swe_link) or sql_error($sql); 
	}
}
echo '<br /><br /><h2>store_updates tables have been optimized.</h2>';
?>