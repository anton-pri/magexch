<?php
/*
 * Vendor: CW
 * addon: amazon
 */
namespace CW\amazon;

const addon_name = 'amazon';
const addon_target = 'amazon_export';
const addon_version = '0.1';

if (APP_AREA == 'admin' && $target == addon_target && !empty($addons[addon_name])) {

    $amazon_config = array(
        'product_id_type'   => 4, // Possible vaules  '' = not specified, 1 = ASIN,    2 = ISBN,    3 = UPC,    4 = EAN
        'product_id'        => 'ean', // Field name where product_id is stored
        'default_item_condition'    => 11, // 11 _ "New" according to amazon doc http://www.amazon.com/gp/help/customer/display.html?nodeId=1161312
        'default_leadtime_to_ship'  => '', // leadtime in days
        'default_ship_internationally' => '', // Values: ['','N','Y']
        'default_expedited_shipping'        => 'n',
        'default_standard_plus'     => '',
        'item_note'                 => 'descr', // Field/attribute name where item-note is stored
        'fulfillment_center_id'     => '',
        'default_product_tax_code'  => '',
    );


    cw_addons_set_controllers(
        array('replace','admin/'.addon_target.'.php','addons/'.addon_name.'/'.addon_target.'.php')
    );
//    cw_set_controller('admin/'.addon_target.'.php','addons/'.addon_name.'/'.addon_target.'.php', EVENT_REPLACE);

    cw_include('addons/'.addon_name.'/func.php');

    cw_addons_set_template(
        array('replace', 'admin/main/amazon_export.tpl', 'addons/amazon/amazon_export.tpl')
    );
    cw_addons_set_template(
        array('replace', 'admin/import_export/amazon_export.tpl', 'addons/amazon/amazon_export.tpl')
    );

}

