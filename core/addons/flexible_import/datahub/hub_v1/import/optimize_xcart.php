<?php 
require_once('header.php');

$swe_link = mysql_connect(SWE_HOST, SWE_DBUSER, SWE_DBPASS) or die('Could not connect to db');//dev host is HOST
mysql_select_db(SWE_XCART_DB, $swe_link) or die('Problem with database');

$result = mysql_list_tables(SWE_XCART_DB);

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
echo '<br /><br /><h2>x-cart tables have been optimized.  Run the import script.</h2>';
?>