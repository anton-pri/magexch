<?php

/*
 * Example PHP implementation used for the index.html example
 */

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

$json_obj = Editor::inst( $db, $tables['datascraper_attributes'] );
/*
| ds_attribute_id | int(5)        | NO   | PRI | NULL    | auto_increment |
| site_id         | int(5)        | NO   |     | NULL    |                |
| name            | varchar(255)  | NO   |     | NULL    |                |
| pattern         | varchar(1000) | NO   |     | NULL    |                |
| type            | varchar(100)  | NO   |     | NULL    |                |
| table_field     | varchar(100)  | NO   |     | NULL    |                |
| table_field_hub | varchar(32)   | NO   |     |         |                |
| mandatory       | tinyint(2)    | NO   |     | 0       |                |
*/

$json_obj->pkey('ds_attribute_id');

$json_obj->fields(
        Field::inst( 'ds_attribute_id' ),
        Field::inst( 'site_id' )->validator( 'Validate::notEmpty' ), 
        Field::inst( 'name' )->validator( 'Validate::notEmpty' ),
        Field::inst( 'pattern' ),
        Field::inst( 'type' ),
        Field::inst( 'table_field' ),
        Field::inst( 'table_field_hub' ),
        Field::inst( 'mandatory' )
);

$json_obj->process( $_POST )->json();

exit;
