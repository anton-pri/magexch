<?php

error_reporting(0);

//if (get_magic_quotes_gpc()) {
function stripslashes_gpc(&$value)
{
$value = stripslashes($value);
}
array_walk_recursive($_GET, 'stripslashes_gpc');
array_walk_recursive($_POST, 'stripslashes_gpc');
array_walk_recursive($_COOKIE, 'stripslashes_gpc');
array_walk_recursive($_REQUEST, 'stripslashes_gpc');
//}
if ($action == "edit") {
    array_walk_recursive($data, 'stripslashes_gpc');
}

// DataTables PHP library
include( "core/addons/flexible_import/datahub/DataTableEdit/php/DataTables.php" );

// Alias Editor classes so they are easy to use
use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate;

$json_obj = Editor::inst( $db, $tables['datahub_update_nonstock_preview'],'table_id');

$uns_tbl_fields = cw_datahub_get_update_nonstock_preview_fields();

foreach ($uns_tbl_fields as $field) {
    $json_obj->fields(Field::inst($field['field'])); 
}

$json_obj->process( $_POST )->json();
exit;
