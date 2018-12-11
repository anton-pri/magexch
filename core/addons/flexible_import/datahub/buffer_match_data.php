<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

$table = cw_datahub_import_buffer_view_table_name();

// Table's primary key
$primaryKey = 'table_id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = cw_datahub_get_buffer_table_fields($table);

$column_ids = array();
foreach ($columns as $col) {
    $column_ids[$col['db']] = $col['dt'];
}

// SQL server connection information
$sql_details = array(
    'user' => $app_config_file['sql']['user'],
    'pass' => $app_config_file['sql']['password'],
    'db'   => $app_config_file['sql']['db'],
    'host' => $app_config_file['sql']['host']
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
cw_include('addons/flexible_import/datahub/ssp.class.php');
 
echo json_encode(
    $sent_data = cw_datahub_prepare_display_buffer(SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns ), $column_ids)
);
//cw_log_add('datahub_buffer_data', array($match_items_column_id, $sent_data));
//print_r(SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns ));
die;
