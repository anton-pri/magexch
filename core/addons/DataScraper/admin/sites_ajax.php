<?php

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

$json_obj = Editor::inst( $db, $tables['datascraper_sites_config'] );
/*
| siteid         | int(5)       | NO   | PRI | NULL    | auto_increment |
| name           | varchar(255) | NO   |     | NULL    |                |
| test_url       | text         | NO   |     | NULL    |                |
| parsed         | int(2)       | NO   |     | 0       |                |
| active         | int(2)       | NO   |     | 0       |                |
| wget_path_list | varchar(255) | NO   |     |         |                |
| wget_run_hrs   | int(11)      | NO   |     | 23      |                |
| wget_run_day   | int(11)      | NO   |     | 1       |                |
| parsing_active | int(2)       | NO   |     | 0       |                |
+----------------+--------------+------+-----+---------+----------------+
*/

$json_obj->pkey('siteid');

$json_obj->fields(
        Field::inst( 'siteid' ),
        Field::inst( 'name' )->validator( 'Validate::notEmpty' ),
        Field::inst( 'test_url' ),
        Field::inst( 'parsed' ),
        Field::inst( 'active' ),
        Field::inst( 'wget_path_list' ),
        Field::inst( 'wget_run_hrs' ),
        Field::inst( 'wget_run_day' ),
        Field::inst( 'parsing_active' )
);

$json_obj->process( $_POST )->json();

exit;
