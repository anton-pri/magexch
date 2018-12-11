<?php
namespace cw\custom_magazineexchange_external_links;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
	$action_function = 'products_details';
}

$product_id = intval($request_prepared['product_id']);
if (!$product_id) {
    return error('Invalid product instance'); // return Error instance
}

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

if ($action_function != 'products_details') products_details_redirect();

/* ================================================================================== */

/* Actions */

/**
 * View product details
 * [target:products][mode:details][action:]
 */
function products_details() {
    global $request_prepared, $smarty;
    
    $product_id = intval($request_prepared['product_id']);
    
    $external_links = cw_call('cw\custom_magazineexchange_external_links\get_by_product_id', array($product_id));
    
    $smarty->assign('external_links',$external_links);
    
    return $external_links;
}

/**
 * Update external links
 * [target:products][mode:external_links][action:update]
 */
function products_external_links_update() {
    global $request_prepared;

    $product_id = intval($request_prepared['product_id']);
    $links = $request_prepared['links'];
    
    foreach ($links as $id=>$link) {
        $link['product_id'] = $product_id;
        $link['id'] = $id;
        $link['price'] = floatval($link['price']);

        cw_call('cw\custom_magazineexchange_external_links\update', array($link));
    }

}

/**
 * Add external links
 * [target:products][mode:external_links][action:add]
 */
function products_external_links_add() {
    global $request_prepared;

    $product_id = intval($request_prepared['product_id']);

    $data = $request_prepared['link'];
    $data['price'] = floatval($data['price']);
    $data['product_id'] = $product_id;
    unset($data['id']);
    
    $external_link = cw_call('cw\custom_magazineexchange_external_links\update', array($data));
    
    return $external_link;

}

/**
 * Delete external links
 * [target:products][mode:external_links][action:delete]
 */
function products_external_links_delete() {
    global $request_prepared;

    $product_id = intval($request_prepared['product_id']);
    $links = $request_prepared['links'];
    
    foreach ($links as $id=>$link) {
        if ($link['delete']) {
            cw_call('cw\custom_magazineexchange_external_links\delete', array($id));
        }
    }
    
}

/* Service functions */

function products_details_redirect() {
    global $request_prepared;
    cw_header_location('index.php?target=products&mode=details&product_id='.$request_prepared['product_id'].'&js_tab=external_links');
}
