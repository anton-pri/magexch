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
 
// DB table to use
$table = $tables['datahub_main_data'];
 
// Table's primary key
$primaryKey = 'ID';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = cw_datahub_prepare_main_columns_popup(cw_datahub_get_main_table_fields());

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

//cw_log_add('dh_item_select_popup_data', array('_POST'=>$_POST, 'sql_details'=>$sql_details, 'table'=>$table, 'primaryKey'=>$primaryKey, 'columns'=>$columns));

$sent_data = SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns ); 

    function dh_array_map_recursive($callback, $array) {
        foreach ($array as $key => $value) {
            if (is_array($array[$key])) {
                $array[$key] = dh_array_map_recursive($callback, $array[$key]);
            }
            else {
                $array[$key] = call_user_func($callback, $array[$key]);
            }
        }
        return $array;
    }

$sent_data_utf8 = dh_array_map_recursive('utf8_encode', $sent_data);
//cw_log_add('dh_item_select_popup_data', array('sent_data'=>$sent_data, 'utf8'=>$sent_data_utf8, 'json'=>json_encode($sent_data)));

echo json_encode($sent_data_utf8);

die;
