<?php
global $smarty, $tables;

if (defined('IS_AJAX') && !empty($review_id)) {
    cw_load('ajax');

    $review = cw_review_get_management_reviews("WHERE $tables[products_reviews].review_id=".$review_id, "", "");
    $smarty->assign('review_item', $review[0]);

    cw_add_ajax_block(array(
        'id' => 'review_item_record_' . $review_id,
        'action' => 'update',
        'template' => 'addons/estore_products_review/admin_reviews_management_item.tpl'
    ));
}

$review_session_data = &cw_session_register('review_session_data', array());
$top_message = &cw_session_register('top_message');

$review_data['page'] = $review_session_data['page'] = (!empty($page) ? $page : 1);
$review_data['sort_field'] = "";
$review_data['sort_direction'] = "";
$where = "";
$orderby = "ORDER BY $tables[products_reviews].ctime";
$limit = "";

if ($action == 'add_reviews') { 

    if (!empty($review_new['message'])) {
        cw_call('cw_review_add_new_review_manual', array($review_new, 0));
        $top_message = array('content' => cw_get_langvar_by_name('msg_adm_products_reviews_upd'), 'type' => 'I');
    }
    cw_header_location("index.php?target=estore_reviews_management&action=process");
}

if ($action == 'delete') {

    if (!empty($checked_review)) {

        foreach ($checked_review as $review_id => $_review) {
            cw_review_delete_review($review_id);
        }
        $top_message = array('content' => cw_get_langvar_by_name('txt_review_nas_been_deleted'), 'type' => 'I');
        cw_header_location("index.php?target=estore_reviews_management&action=process");
    }
}

if ($action == 'reset') {
    $review_session_data['search'] = array();
    $review_session_data['ids'] = "";
    cw_header_location("index.php?target=estore_reviews_management");
}

if ($action == "search") {
    $review_session_data = $review_data;
    $where = cw_review_generate_where_search($review_data);
}

if ($action == "process") {
    $review_data = $review_session_data;
    $where = cw_review_generate_where_search($review_data);

    $avail_sort_fields = array('ctime', 'productcode', 'status');

    if (in_array($sort, $avail_sort_fields)) {
        $orderby = "ORDER BY " . $sort;

        if ($direction == 1) {
            $orderby .= " DESC";
        }
        $review_data['sort_field'] = $sort;
        $review_data['sort_direction'] = $direction;
    }
}

$total_items = cw_review_get_management_reviews_count($where, $orderby, "");

$navigation = cw_core_get_navigation($target, $total_items, $page);
$navigation['script'] = "index.php?target=estore_reviews_management&action=process";
if (!empty($review_data['sort_field'])) {
    $navigation['script'] .= "&sort=" . $review_data['sort_field'];
}
if ($review_data['sort_direction'] != "") {
    $navigation['script'] .= "&direction=" . $review_data['sort_direction'];
}
$smarty->assign('navigation', $navigation);

$limit = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
$reviews = cw_review_get_management_reviews($where, $orderby, $limit);

$smarty->assign('reviews', $reviews);
$smarty->assign('review_data', $review_data);

$smarty->assign('target_reviews_management', 'index.php?target=estore_reviews_management');
$smarty->assign('use_add_form', 1);

$smarty->assign('current_section_dir', 'main');
$smarty->assign('main', 'estore_reviews_management');
