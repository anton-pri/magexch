<?php

if (!defined('APP_START')) { die('The software application has not been initialized.'); }


do {

    if (!isset($addons['cms'])) {
        break;
    }

	if (AREA_TYPE != 'A') {
    	break;
    }

    global $product_id, $mode, $action;

    if (!isset($product_id)) {
        $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : $product_id;
    }
    $product_id = (int) $product_id;


    if (!isset($mode)) {
        $mode = isset($_GET['mode']) ? $_GET['mode'] : null;
        $mode = isset($_POST['mode']) ? $_POST['mode'] : $mode;
    }
    $mode = (string) $mode;


    if (!isset($action)) {
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        $action = isset($_POST['action']) ? $_POST['action'] : $action;
    }
    $action = (string) $action;


    if (empty($product_id)) {
        break;
    }

    $addon_actions = array(
        'ab_update'    => 'ab_update',
        'ab_show'      => 'ab_show'
    );

    
    if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            cw_func_call('ab_show', $product_id);
        }
        break;
    }

    $smarty->assign('action', $action);
    cw_func_call($addon_actions[$action], $product_id);

} while (0);



function ab_show($product_id) {
    global $smarty, $tables, $config;
    global $current_language;


	$product_id = (int) $product_id;

    if (empty($product_id)) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        ab_redirect($product_id);
    }

    $contentsections = cw_query("SELECT ab.contentsection_id, ab.service_code, ab.name, ab.type, abp.object_id as selected FROM $tables[cms] ab
            LEFT JOIN $tables[cms_restrictions] abp ON ab.contentsection_id = abp.contentsection_id AND abp.object_id='$product_id' AND abp.object_type='P'
            ORDER BY ab.service_code");

    $smarty->assign('contentsections', $contentsections);

}


function ab_update($product_id) {
    global $tables, $top_message, $smarty, $contentsections;

    $product_id = (int) $product_id;

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        ab_redirect($product_id);
    }

    db_query("DELETE FROM $tables[cw_cms_restrictions] WHERE object_id='$product_id' AND object_type='P'");

    foreach ($contentsections as $bid=>$v)
        db_query ("INSERT INTO $tables[cw_cms_restrictions] SET object_id='$product_id', object_type='P', contentsection_id='$bid'");

    $top_message = array('content' => 'Product content sections updated successfully', 'type' => 'I');

    ab_redirect($product_id);
}


function ab_redirect($product_id = 0) {
    global $app_catalogs, $target, $mode;

    $productid_url_param = null;

    if (!empty($product_id)) {
        $product_id = (int) $product_id;
        $productid_url_param = '&product_id=' . $product_id;
    }

    cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=$mode&js_tab=cms$productid_url_param");
}
