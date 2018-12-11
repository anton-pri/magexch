<?php
namespace CW\amazon;

/* ======================================== */
/*
 * Controller init
 *
 */

cw_load('product','category','image','attributes','config');

global $mode;

if (empty($mode)) {
    $mode = 'index';
}
if (!in_array($mode, array('index','export'), true))
    return false;

$smarty->assign('main','amazon_export');

// Call corresponding action
cw_call('CW\amazon\\'.$mode);


return true;


/* ======================================== */
/*
 * Implementation of actions
 *
 */

// Show export page
function index() {
    global $smarty;
    global $top_message;
    global $amazon_config;

//global $config; cw_var_dump($config['amazon']);


}

function export() {
    global $REQUEST_METHOD, $smarty, $config, $addons, $top_message, $customer_id, $tables;
    global $mode, $action, $amazon_config;

    $success = false;

    if ($REQUEST_METHOD != 'POST') cw_header_location('index.php?target='.addon_target);

    if ($_POST['export_type'] == 'PaQ' && (empty($_POST['price']) && empty($_POST['quantity']))) {
        $top_message = array('content'=>'At least Price or Quantity must be exported.', 'type'=>'E');
        cw_header_location('index.php?target='.addon_target);
        return false;
    }

    $_filename = 'files/amazon/'.date('Ymd').'_'.date('His').'_'.$_POST['export_type'].'.csv';

    if (($filename = cw_allow_file($_filename, true)) && $file = cw_fopen($_filename, 'w', true)) {

    $pids = cw_call('cw_objects_get_list_ids',array('P'));
    if (empty($pids)) {
         $pids = cw_query_column("SELECT product_id FROM $tables[products] WHERE status=1"); // Very bad. Use API
    }

    if ($pids) {

        $amazon_config = cw_array_merge($amazon_config, $config['amazon'], $_POST);
        cw_config_update('amazon',$_POST);

        $warnings = array();
        $data = array();
        $header_put = false;

        foreach($pids as $v) {
            $variants = array();
            $prod = cw_func_call('cw_product_get',array('id'=>$v,'info_type'=>8|64|128|256|512|2048));


            $attr = cw_query_hash("SELECT a.field, av.value
                    FROM $tables[attributes_values] av, $tables[attributes] a
                    WHERE av.item_id=$v AND av.item_type='P' AND a.attribute_id=av.attribute_id",'field',false, true); // very bad. Use API



            if ($prod['is_variants']) {
                $variants = cw_call('cw_get_product_variants',array($v));
            }
            else {
                $variants[0] = $prod;
            }

            foreach ($variants as $var) {

                $var = cw_array_merge($var,$attr);

                if ($_POST['export_type'] == 'PaQ') {
                    $data = array('sku'=>$var['productcode'], 'price'=>$_POST['price']?$var['price']:'', 'quantity'=>$_POST['quantity']?$var['avail']:'','leadtime-to-ship'=>$amazon_config['default_leadtime_to_ship']);
                }
                if ($_POST['export_type'] == 'InvLoad') {
                    $data = array(
                    'sku'               => $var['productcode'],
                    'product-id'        => empty($amazon_config['product_id_type'])?'':(string)$var[$amazon_config['product_id']],
                    'product-id-type'   => $amazon_config['product_id_type'],
                    'price'             => $var['price'],
                    'item-condition'    => empty($var[$amazon_config['item_condition']])?$amazon_config['default_item_condition']:$var[$amazon_config['item_condition']],
                    'quantity'          => empty($amazon_config['fulfillment_center_id'])?$var['avail']:'', // Do not suply quantity if fulfilment service is used
                    'add-delete'        => empty($_POST['add-delete'])?'a':$_POST['add-delete'],            // Default action is 'a' _ update/add; 'd' _ delete from stock; 'x' _ delete product record
                    'will-ship-internationally' => empty($var[$amazon_config['ship_internationally']])?$amazon_config['default_ship_internationally']:$var[$amazon_config['ship_internationally']],
                    'expedited-shipping'        => empty($var[$amazon_config['expedited_shipping']])?$amazon_config['default_expedited_shipping']:$var[$amazon_config['expedited_shipping']],
                    'standard-plus'     => empty($var[$amazon_config['standard_plus']])?$amazon_config['default_standard_plus']:$var[$amazon_config['standard_plus']],
                    'item-note'         => $var[$amazon_config['item_note']],
                    'fulfillment-center-id'     => $amazon_config['fulfillment_center_id'],
                    'product-tax-code'  => $amazon_config['default_product_tax_code'],
                    'leadtime-to-ship'  => $amazon_config['default_leadtime_to_ship'],
                    );
                }

                if (!$header_put) {
                    fputcsv($file, array_keys($data), "\t");
                    $header_put = true;
                }

                fputcsv($file, $data, "\t");
            }

        }

    }

	fclose($file);
    $top_message = array('content' => 'File <b>'.$_filename.'</b> successfully created');
    }

    cw_header_location('index.php?target='.addon_target);
}

/* ======================================== */
