<?php
namespace cw\custom_magazineexchange_sellers;

if ($REQUEST_METHOD == "POST") {

	exit;
}

if (defined('IS_AJAX') && $mode == 'get_subcats') {
    $parent_id = isset($parent) ? intval($parent) : 0;
    $cat_resp = cw_query("SELECT c.*, IFNULL(c_lng.category, c.category) category_name 
        FROM $tables[categories] c 
        LEFT JOIN $tables[categories_lng] c_lng ON c.category_id=c_lng.category_id AND c_lng.code='EN'
        WHERE c.parent_id=$parent_id 
        HAVING category_name != '' 
        ORDER BY category_name ASC 
        LIMIT 1000");


    global $user_account;
    
    foreach ($cat_resp as $cr_k => $cr_val) {
        $cat_resp[$cr_k]['allowed'] = mag_seller_is_category_allowed_for_seller($user_account['membership_id'], $cr_val['category_id'], $access_note);
        $cat_resp[$cr_k]['category_type'] = cw_call('magexch_get_attribute_value', array('C', $cr_val['category_id'], 'magexch_category_type'));
    }    

    define('PREVENT_SESSION_SAVE', true);
    echo json_encode($cat_resp);
    exit();
}


$smarty->assign('current_main_dir',     'addons/' . addon_name);
$smarty->assign('current_section_dir',  'seller');
$smarty->assign('main', 'seller_category_selector');
$smarty->assign('home_style', 'iframe');

define('PREVENT_XML_OUT', true); // need simple HTML out if controller called as ajax via $.load()
