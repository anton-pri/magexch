<?php 
global $app_config_file;

	define('HOST', $app_config_file['sql']['host']);
	define('DBUSER', $app_config_file['sql']['user']);
	define('DBPASS', $app_config_file['sql']['password']);	
	define('STORE_UPDATES', $app_config_file['sql']['db']);		
	//define('DWB_XCART_DB', 'xcartDB');		
	
	define('SWE_HOST', $app_config_file['sql']['host']);
	define('SWE_DBUSER', $app_config_file['sql']['user']);
	define('SWE_DBPASS', $app_config_file['sql']['password']);	
	//define('SWE_STORE_UPDATES', 'xcartDB');		
	define('SWE_XCART_DB', $app_config_file['sql']['db']);
?>
