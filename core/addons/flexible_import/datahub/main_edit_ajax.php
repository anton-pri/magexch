<?php

/*
 * Example PHP implementation used for the index.html example
 */
//print(getcwd()); 
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

// Build our Editor instance and process the data coming from _POST

function upload_files_custom_filter($data, $upload_files) {
    global $tables; 
    $img_key_field = 'cimageurl';
    $img_tbl_name = $tables['datahub_main_data_images'];
    $result = array($img_tbl_name => array()); 
    foreach ($data as $d) {
        if (isset($upload_files[$img_tbl_name][$d[$img_key_field]])) 
            $result[$img_tbl_name][$d[$img_key_field]] = $upload_files[$img_tbl_name][$d[$img_key_field]];
        else
            $result[$img_tbl_name][$d[$img_key_field]] = array('id' => $d[$img_key_field],  'filename' => 'images/no_image.jpg', 'filesize' => 0, 'web_path' => 'images/no_image.jpg', 'system_path' => 'images/no_image.jpg'); 
    }
    return $result;
}

$json_obj = Editor::inst( $db, $tables['datahub_main_data'] );
$json_obj->fields(
        Field::inst( 'ID' ),
        Field::inst( 'catalog_id'), 
        Field::inst( 'dup_catid' )->validator( 'Validate::notEmpty' ),
        Field::inst( 'Producer' )
);

global $app_dir;

$json_obj->fields(
        Field::inst( 'name' ),
        Field::inst( 'Vintage' ),
        Field::inst( 'Size' ),
        Field::inst( 'cimageurl' )
            ->setFormatter( 'Format::ifEmpty', null )
            ->upload( Upload::inst( $app_dir.'/images/swe_new_hub__ID__.__EXTN__' )
                ->db( $tables['datahub_main_data_images'], 'id', array(
                    'filename'    => Upload::DB_FILE_NAME,
                    'filesize'    => Upload::DB_FILE_SIZE,
                    'web_path'    => Upload::DB_WEB_PATH,
                    'system_path' => Upload::DB_SYSTEM_PATH
                ) )
                ->validator( function ( $file ) {
                    return $file['size'] >= 5000000 ?
                        "Files must be smaller than 5M" :
                        null;
                } )
                ->allowedExtensions( array( 'png', 'jpg', 'gif' ), "Please upload an image" )
            ),
        Field::inst( 'TareWeight' ),
        Field::inst( 'LongDesc' ),
        Field::inst( 'Region' ),
        Field::inst( 'drysweet' ),
        Field::inst( 'country' ),
        Field::inst( 'keywords' ),
        Field::inst( 'varietal' ),
        Field::inst( 'Appellation' ),
        Field::inst( 'sub_appellation' )
);

//cw_log_add('dh_main_edit_ajax', var_dump($json_obj));

$json_obj->fields(
        Field::inst( 'RP_Rating' ),
        Field::inst( 'RP_Review' ),
        Field::inst( 'WS_Rating' ),
        Field::inst( 'WS_Review' ),
        Field::inst( 'WE_Rating' ),
        Field::inst( 'WE_Review' ),
        Field::inst( 'DC_Rating' ),
        Field::inst( 'DC_Review' ),
        Field::inst( 'ST_Rating' ),
        Field::inst( 'ST_Review' ),
        Field::inst( 'W_S_Rating' ),
        Field::inst( 'W_S_Review' ),
        Field::inst( 'BTI_Rating' ),
        Field::inst( 'BTI_Review' ),
        Field::inst( 'Winery_Rating' ),
        Field::inst( 'Winery_Review' ),
        Field::inst( 'CG_Rating' ),
        Field::inst( 'CG_Review' ),
        Field::inst( 'JH_Rating' ),
        Field::inst( 'JH_Review' ),
        Field::inst( 'MJ_Rating' ),
        Field::inst( 'MJ_Review' ),
        Field::inst( 'TWN_Rating' ),
        Field::inst( 'TWN_Review' ),
        Field::inst( 'bot_per_case' ),
        Field::inst( 'initial_xref' ),
        Field::inst( 'price' ),
        Field::inst( 'twelve_bot_price' ),
        Field::inst( 'cost' ),
        Field::inst( 'cost_per_case' ),
        Field::inst( 'stock' ),
        Field::inst( 'store_stock'),
        Field::inst( 'split_case_charge'),
        Field::inst( 'manual_price'),
        Field::inst( 'twelve_bot_manual_price'),
        Field::inst( 'supplier_id'),
        Field::inst( 'min_price'),
        Field::inst( 'weight'),
        Field::inst( 'avail_code'), 
        Field::inst( 'hide'),
        Field::inst( 'store_sku'),
        Field::inst( 'minimumquantity'),
        Field::inst( 'meta_description'), 
        Field::inst( 'Source'),
        Field::inst( 'wholesaler')
    );
$json_obj->process( $_POST )->json();


/*
$json_obj = 
Editor::inst( $db, 'cw_datahub_main_data' )
    ->fields(
        Field::inst( 'ID' ),
        Field::inst( 'dup_catid' )->validator( 'Validate::notEmpty' ),
        Field::inst( 'Producer' ),
        Field::inst( 'name' ),
        Field::inst( 'Vintage' ),
        Field::inst( 'Size' ),
        Field::inst( 'cimageurl' )
            ->setFormatter( 'Format::ifEmpty', null )
            ->upload( Upload::inst( '/home/devsarat/public_html/images/swe__ID__.__EXTN__' )
                ->db( 'cw_datahub_main_data_images', 'id', array(
                    'filename'    => Upload::DB_FILE_NAME,
                    'filesize'    => Upload::DB_FILE_SIZE,
                    'web_path'    => Upload::DB_WEB_PATH,
                    'system_path' => Upload::DB_SYSTEM_PATH
                ) )
                ->validator( function ( $file ) {
                    return $file['size'] >= 5000000 ?
                        "Files must be smaller than 5M" :
                        null;
                } )
                ->allowedExtensions( array( 'png', 'jpg', 'gif' ), "Please upload an image" )
            ),
        Field::inst( 'TareWeight' ),
        Field::inst( 'LongDesc' ), 
        Field::inst( 'Region' ),
        Field::inst( 'drysweet' ),
        Field::inst( 'country' ),
        Field::inst( 'keywords' ),
        Field::inst( 'varietal' ),
        Field::inst( 'Appellation' ),
        Field::inst( 'sub_appellation' ),
        Field::inst( 'RP_Rating' ),
        Field::inst( 'RP_Review' ),
        Field::inst( 'WS_Rating' ),
        Field::inst( 'WS_Review' ),
        Field::inst( 'WE_Rating' ),
        Field::inst( 'WE_Review' ),
        Field::inst( 'DC_Rating' ),
        Field::inst( 'DC_Review' ),
        Field::inst( 'ST_Rating' ),
        Field::inst( 'ST_Review' ),
        Field::inst( 'W_S_Rating' ),
        Field::inst( 'W_S_Review' ),
        Field::inst( 'BTI_Rating' ),
        Field::inst( 'BTI_Review' ),
        Field::inst( 'Winery_Rating' ),
        Field::inst( 'Winery_Review' ),
        Field::inst( 'CG_Rating' ),
        Field::inst( 'CG_Review' ),
        Field::inst( 'JH_Rating' ),
        Field::inst( 'JH_Review' ),
        Field::inst( 'MJ_Rating' ),
        Field::inst( 'MJ_Review' ),
        Field::inst( 'TWN_Rating' ),
        Field::inst( 'TWN_Review' ),
        Field::inst( 'bot_per_case' ),
        Field::inst( 'initial_xref' ),
        Field::inst( 'price' ),
        Field::inst( 'twelve_bot_price' ),
        Field::inst( 'cost' ),
        Field::inst( 'cost_per_case' ),
        Field::inst( 'stock' ),
        Field::inst( 'store_stock'),
        Field::inst( 'split_case_charge'),
        Field::inst( 'manual_price'),
        Field::inst( 'twelve_bot_manual_price'),
        Field::inst( 'supplier_id'),
        Field::inst( 'min_price'),
        Field::inst( 'weight'),
        Field::inst( 'avail_code'), 
        Field::inst( 'hide')
    )
    ->process( $_POST )
    ->json();
*/
exit;
