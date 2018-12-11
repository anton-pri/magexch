<?php
namespace cw\DataScraper;
/*
 * Example PHP implementation used for the index.html example
 */
//print(getcwd()); 

// DataTables PHP library
include( "core/addons/DataScraper/include/DataTableEdit/php/DataTables.php" );
 
// Alias Editor classes so they are easy to use
use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate;

// Build our Editor instance and process the data coming from _POST
$curr_site_id = &cw_session_register('curr_site_id',0);
if (!$curr_site_id)
    $curr_site_id = cw_query_first_cell("SELECT siteid FROM $tables[datascraper_sites_config] ORDER BY name LIMIT 1");

$site_results_tbl = $tables['datascraper_result_values'].$curr_site_id;

$json_obj = Editor::inst( $db, $site_results_tbl );

$json_obj->pkey('result_id');

$results_tbl_fields = cw_call('cw\\'.addon_name.'\\cw_datascraper_get_table_fields', array('result_values_'.$curr_site_id));

foreach ($results_tbl_fields as $res_field) {
    $json_obj->fields(Field::inst( $res_field['field'] ));
}

$json_obj->process( $_POST )->json();

exit;
