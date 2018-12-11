<?php
//include "./mysql_access/import/config_hub.php";
ini_set("display_errors","2");
ERROR_REPORTING(E_ALL);

	require_once 'db_info.php';
	
	//$dwb_link = open_connection();
	
	define('CLASSES', 'classes/');
	define('FEED_FILE', '/home/saratdev/public_html/files/import_feeds/');	
	define('DB_BU_DIR', 'db_bu/');	
	define('DB_BU_FILE', 'store_updates.sql');

if (!function_exists('mysql_num_rows')) {
function mysql_num_rows($result) {
    return mysqli_num_rows($result);
}
}
	
if (!function_exists('mysql_query')) {
function mysql_query($query) {
    global $__mysql_connection_id;
    $mysql_connection_id = $__mysql_connection_id;

    $result = mysqli_query($mysql_connection_id, $query);

//    print($query."<br><br>"); 

    return $result;
}
}

if (!function_exists('mysql_real_escape_string')) {
function mysql_real_escape_string($escapestr) {
    global $__mysql_connection_id;
    $mysql_connection_id = $__mysql_connection_id;

    $result = mysqli_real_escape_string($mysql_connection_id, $escapestr);
    return $result;
}	
}

if (!function_exists('mysql_fetch_array')) {
function mysql_fetch_array($result, $result_type = MYSQLI_BOTH) {
    return mysqli_fetch_array($result, $result_type);
}
}

if (!function_exists('mysql_errno')) {
function mysql_errno() {
    global $__mysql_connection_id;
    $mysql_connection_id = $__mysql_connection_id;

    return mysqli_errno($mysql_connection_id);
}
}

if (!function_exists('mysql_fetch_assoc')) {
function mysql_fetch_assoc($result) {
    return mysqli_fetch_assoc($result);
}
}

if (!function_exists('mysql_error')) {
function mysql_error() {
    global $__mysql_connection_id;
    $mysql_connection_id = $__mysql_connection_id;
    return mysqli_error($mysql_connection_id);
} 
}
	
function exportMysqlToXls($table,$filename = 'export.xls', $sql) {
	$skip_index = array(7, 18, 24);//we're skipping Regular Price, Qty 1 and Custom Field 4	
	$result = mysql_query("SHOW COLUMNS FROM ".$table."");
	$csv_output = '';
	/* Fetch The Attributes of Each Field In Table */
	
	$i = 0;
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			if(!in_array($i, $skip_index)) {
				$csv_output .= $row['Field']."\t";
			}			
			$i++;
		}
	}

	$csv_output .= "\n";
	
	/* Select All Fields Within Table and Print Them */
	
	$values = mysql_query($sql);
	while ($row = mysql_fetch_row($values)) {
		for ($j = 0; $j < $i; $j++) {
		/**
		 * @todo change using a hard coded number for the final field
		 */		


//			if($j == 2) {
//				$order   = array("\r\n", "\n", "\r");
//				$row[$j] = str_replace($order, ' ', $row[$j]);
//			}

			if(!in_array($j, $skip_index)) {
				if($j == 25) {
					$pos = strpos($row[25], '-');				
					if ($pos !== false) {				
						$row[25] = preg_replace('/-/', '- ', $row[25], 1);//add	a space after the first -
					} 
				}			
				$csv_output .= $row[$j]."\t";			
			}
	

		}
		$csv_output .= "\n";  
	}

 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
 header("Cache-Control: no-store, no-cache, must-revalidate"); 
 header("Cache-Control: post-check=0, pre-check=0", false);
 header("Pragma: no-cache");
 	
	header("Content-type: application/x-msexcel"); 
	header("Content-Length: " . strlen($csv_output)); 
	header("Content-disposition: attachment; filename=".$filename); 
	header("Pragma: no-cache"); 
	header("Expires: 0");
	
	echo $csv_output;
	return;	
}
	
	//this opens the main connection to the hub db
	function open_connection() {
		$dwb_link = mysql_connect('localhost', DBUSER, DBPASS) or die('Could not connect to db');
		mysql_select_db(STORE_UPDATES, $dwb_link) or die('Problem with database');	
		//mysql_query("SET NAMES 'utf8'");
		return $dwb_link;
	}

	function open_xcart_connection($store_id) {
	
//		$link = mysql_connect(SWE_HOST, SWE_DBUSER, SWE_DBPASS) or die('Could not connect to db');
//		mysql_select_db(SWE_XCART_DB, $link) or die('Problem with database');
				
		if($store_id == 1) {
			$link = mysql_connect(SWE_HOST, SWE_DBUSER, SWE_DBPASS) or die('Could not connect to db');
			mysql_select_db(SWE_XCART_DB, $link) or die('Problem with database');		
		}
		elseif ($store_id == 2) {
			$link = mysql_connect(HOST, DBUSER, DBPASS) or die('Could not connect to db');
			mysql_select_db(DWB_XCART_DB, $link) or die('Problem with database');	
		}
		//mysql_query("SET NAMES 'utf8'");		
		return $link;
	}

	function __autoload($class_name) {
		require_once CLASSES . $class_name . '.class.php';
	}
	
	function right($value, $count){
		return substr($value, ($count*-1));
	}
	
	function left($string, $count){
		return substr($string, 0, $count);
	}

	//function sql_error($sql = '', $file, $class = '', $function = '') {
	function sql_error($sql = '') {	
	 	//	var_dump(debug_backtrace());	
	 	$errors = debug_backtrace();
	 	foreach ($errors as $k => $v) {
	 		foreach($v as $a => $b) {
	 			echo "$a => $b<br />";
	 		}
	 	}
		die('<br />' . mysql_errno() . ': ' . mysql_error() . '<br /><br />'. $sql);   			
	
	}	
	
	function handleError($errno, $errstr,$error_file,$error_line) { 
		echo "<b>Error:</b> [$errno] $errstr - $error_file:$error_line";
		echo "<br />";
		echo "Terminating PHP Script";
		die();
		//call like this:
//		print_r($data);
//		set_error_handler("handleError");		
	}								
/**
 * convert ms access date to mysql date
 *
 * @param unknown_type $date
 * @return unknown
 */
	function convert_datetime($date) {
		$hour = $minute = $second = 0;
	//list($date, $time) = explode(' ', $str);
		list($month, $day, $year) = explode('/', $date);
		//list($hour, $minute, $second) = explode(':', $time);
		
		return "$year-$month-$day";
	}	
										
$feed_tables = array(
										'acme_feed' => 'acme_feed', 
										'angels_share_feed' => 'angels_share_feed', 
										'bear_feed' => 'bear_feed', 
										'BevAccessFeeds' => 'BevAccessFeeds',
										'bevaccess_supplement' => 'bevaccess_supplement',
										'bowler_feed' => 'bowler_feed', 
										'BWL_feed' => 'BWL_feed',
										'cavatappi_feed' => 'cavatappi_feed',
										'cellar_feed' => 'cellar_feed',
										'Cordon_feed' => 'Cordon_feed',
										'cru_feed' => 'cru_feed', 
										'Domaine_feed' => 'Domaine_feed',
										'DWB_store_feed' => 'DWB_store_feed',
										'EBD_feed' => 'EBD_feed',
										'grape_feed' => 'grape_feed',
										'noble_feed' => 'noble_feed',
										'Polaner_feed' => 'Polaner_feed',
										'Skurnik_feed' => 'Skurnik_feed',
										'SWE_store_feed' => 'SWE_store_feed',
										'Touton_feed' => 'Touton_feed',
										'triage_feed' => 'triage_feed',										
										'vehr_feed' => 'vehr_feed',
										'verity_feed' => 'verity_feed',
										'vias_feed' => 'vias_feed',
										'vinum_feed' => 'vinum_feed',
										'vision_feed' => 'vision_feed',
										'wildman_feed' => 'wildman_feed',
                                                                                'cw_import_feed' => 'cw_import_feed',
										);	

$remove = array('DWB_store_feed', 'SWE_store_feed', 'bevaccess_supplement');											
$update_only_tables	= $feed_tables;				
foreach ($update_only_tables as $k => $v)	{
	if(in_array($k, $remove)) {	
		unset($update_only_tables["$k"]);
	}
}
															
//$GLOBALS['feed_tables'] = $feed_tables;



	
	
