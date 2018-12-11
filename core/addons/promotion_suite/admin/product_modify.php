<?php
if (!defined('APP_START')) { die('The software application has not been initialized.'); }

do {

    if (!isset($addons['promotion_suite'])) {
        return;
    }

	if (AREA_TYPE != 'A') {
    	return;
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
        'ps_bundle_update'    => 'cw_ps_bundle_update',
        'ps_bundle_show'      => 'cw_ps_bundle_show'
    );

    
    if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            cw_call('cw_ps_bundle_show', array($product_id));
        }
        return;
    }

    $smarty->assign('action', $action);
    cw_call($addon_actions[$action], array($product_id));

} while (0);



function cw_ps_bundle_show($product_id) {
    global $smarty, $tables, $config;

	$product_id = (int) $product_id;

    if (empty($product_id)) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        cw_ps_bundle_redirect($product_id);
    }

    $offer_id = cw_query_first_cell("SELECT offer_id FROM $tables[ps_offers] WHERE pid='$product_id' AND active='1'");
	
	$product_offer = cw_call('cw_ps_offer', array($offer_id));

	unset($product_offer['conditions']['P']['products'][$product_id]);
	
	$smarty->assign('product_offer', $product_offer);
	
	return $product_offer;
}


function cw_ps_bundle_update($product_id) {
    global $tables, $config;

    $product_id = (int) $product_id;

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        cw_ps_bundle_redirect($product_id);
    }

	$offer_id = cw_call('cw_ps_offer_bundle_update',array($product_id, $_POST));
	
	// Delete selected products
	if (is_array($_POST['del_cond'])) {
		foreach ($_POST['del_cond'] as $k => $v) {
			$k = intval($k);
			db_query("DELETE FROM $tables[ps_cond_details] WHERE offer_id='$offer_id' AND object_id='$k' AND object_type='".PS_OBJ_TYPE_PRODS."'");
			db_query("DELETE FROM $tables[ps_bonus_details] WHERE offer_id='$offer_id' AND object_id='$k' AND object_type='".PS_OBJ_TYPE_PRODS."'");
		}
	}

	$cond_products = cw_query_column("SELECT object_id FROM $tables[ps_cond_details] WHERE offer_id='$offer_id' AND object_type='".PS_OBJ_TYPE_PRODS."'");
	if (count($cond_products)<=1) {
		//delete offer
		cw_call('cw_ps_offer_delete', array($offer_id));
	}

	cw_array2update('ps_offers', array('auto'=>0), "offer_id='$offer_id'");

// TODO: Domain assignation

    cw_ps_bundle_redirect($product_id);
}


function cw_ps_bundle_redirect($product_id = 0) {
    global $app_catalogs, $target, $mode;

    $productid_url_param = null;

    if (!empty($product_id)) {
        $product_id = (int) $product_id;
        $productid_url_param = '&product_id=' . $product_id;
    }

    cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=$mode&js_tab=bundle$productid_url_param");
}
