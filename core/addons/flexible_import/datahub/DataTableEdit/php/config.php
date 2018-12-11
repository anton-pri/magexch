<?php if (!defined('DATATABLES')) exit(); // Ensure being used in DataTables env.

// Enable error reporting for debugging (remove for production)
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(0);
ini_set('display_errors', '1');


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Database user / pass
 */

$sql_details = array(
	"type" => "Mysql",  // Database type: "Mysql", "Postgres", "Sqlite" or "Sqlserver"
	"user" => $app_config_file['sql']['user'],       // Database user name
	"pass" => $app_config_file['sql']['password'],       // Database password
	"host" => $app_config_file['sql']['host'],       // Database host
	"port" => "",       // Database connection port (can be left empty for default)
	"db"   => $app_config_file['sql']['db'],       // Database name
	"dsn"  => "charset=utf8"        // PHP DSN extra information. Set as `charset=utf8` if you are using MySQL
);


