<?php
global $smarty, $tables, $current_language, $config;

$clean_urls_data = &cw_session_register('clean_urls_data', array());
$top_message = &cw_session_register('top_message');

// history list tab
if ($mode == "history") {

    if ($h_action == "delete" && !empty($history_url)) {

        foreach ($history_url as $history_url_id => $_v) {
            cw_clean_url_delete_url_by_id($history_url_id);
        }
    }

    $smarty->assign('h_sort_field', "");
    $smarty->assign('h_sort_direction', 0);

    $orderby = "ORDER BY h.ctime";
    if (!empty($sort_field)) {
        $orderby = "ORDER BY " . $sort_field;
        $smarty->assign('h_sort_field', $sort_field);
    }
    if ($sort_direction != "") {
        if (!empty($orderby)) {
            $orderby .= $sort_direction ? " ASC" : " DESC";
        }
        $smarty->assign('h_sort_direction', abs($sort_direction - 1));
    }

    $clean_urls_history_list = cw_clean_url_get_clean_urls_history_list_data($orderby);
    $smarty->assign('clean_urls_history_list', $clean_urls_history_list);
}
else {
    $page = (!empty($page) ? $page : 1);

    // edit clean url
    if ($action == "edit") {

        if (
            !empty($new_url)
            && !empty($attribute_id)
            && !empty($item_id)
            && !empty($item_type)
        ) {
            $language = !empty($current_language) ? $current_language : $config['default_customer_language'];
            $result = cw_clean_url_check_url_and_save_value($new_url, $item_id, $item_type, $attribute_id, $language);
            exit($result);
        }

        exit("");
    }

    // delete owner clean url
    if ($action == "delete" && !empty($clean_url_delete_data)) {
        $language = !empty($current_language) ? $current_language : $config['default_customer_language'];

        foreach ($clean_url_delete_data as $clean_url_data => $_v) {
            list($attribute_id, $item_id) = explode("_", $clean_url_data);
            db_query("
                DELETE FROM $tables[attributes_values]
                WHERE item_id = '$item_id' AND attribute_id = '$attribute_id' AND code = '$language'
                    AND (item_type = 'O' OR item_type = 'OS')
            ");
        }
        $top_message = array('content' => cw_get_langvar_by_name('txt_redirect_rule_deleted'), 'type' => 'I');
    }

    // add owner clean url
    if ($action == "add" && !empty($clean_urls_add_data)) {

        if (
            !empty($clean_urls_add_data['dinamic_url'])
            && trim($clean_urls_add_data['dinamic_url']) != 'search'
            && !empty($clean_urls_add_data['static_url'])
        ) {
            $dinamic_url = cw_clean_url_adjust($clean_urls_add_data['dinamic_url']);
            $static_url = trim($clean_urls_add_data['static_url'], '\ /');
            $language = !empty($current_language) ? $current_language : $config['default_customer_language'];
            $attribute_id = cw_query_first_cell("
                SELECT attribute_id FROM $tables[attributes] WHERE field='clean_url' AND item_type = 'O'
            ");
            $item_id = cw_query_first_cell("
                SELECT MAX(item_id) FROM $tables[attributes_values] WHERE attribute_id='$attribute_id' AND item_type = 'O'
            ") + 1;
            $params = array(
                'item_id' => $item_id,
                'item_type' => "O",
                'attribute_id' => $attribute_id,
                'code' => $language
            );
            $params['value'] = cw_clean_url_check_and_generate($dinamic_url, $params, $language);
            cw_array2insert('attributes_values', $params);
            $params['item_type'] = "OS";
            $params['value'] = $static_url;
            cw_array2insert('attributes_values', $params);

            $top_message = array('content' => cw_get_langvar_by_name('txt_new_redirect_rule_added'), 'type' => 'I');
        }
        else {
            $top_message = array('content' => cw_get_langvar_by_name('txt_error_new_redirect_rule_added'), 'type' => 'W');
        }
    }

    // reset filter
    if ($action == "reset") {
        $clean_urls_data = array();
    }

    // filter
    $where = "";
    if (
        $action == 'search' && !empty($clean_urls_search)
        || !empty($clean_urls_data['search'])
    ) {
        if ($action != 'search') {
            $clean_urls_search = $clean_urls_data['search'];
        }

        if (!empty($clean_urls_search['type'])) {
            $attribute  = cw_call('cw_attributes_filter',array(array('field'=>'clean_url','item_type'=>$clean_urls_search['type']), true));
            $where .= " AND a.attribute_id = '$attribute[attribute_id]'";
        }

        // condition for 'entity' and 'to URL' fields
        $substr = trim($clean_urls_search['substring']);
        if ($substr != "" && strlen($substr) > 1) {
            $where .= " AND (av.value LIKE '%" . $substr . "%'
                OR IF (a.item_type = 'C', CONCAT('index&cat=', c.category), '') LIKE '%" . $substr . "%'
                OR IF (a.item_type = 'M', CONCAT('manufacturers&manufacturer_id=', m.manufacturer), '') LIKE '%" . $substr . "%'
                OR IF (a.item_type = 'P', CONCAT('product&product_id=', pr.product), '') LIKE '%" . $substr . "%'
                OR IF (a.item_type = 'S', CONCAT('pages&page_id=', p.name), '') LIKE '%" . $substr . "%'
                OR IF (a.item_type = 'O', 'Owner', '') LIKE '%" . $substr . "%')";
        }

        $clean_urls_data['search'] = $clean_urls_search;
    }

    $get_count = TRUE;
    $total_items = cw_clean_url_get_clean_urls_list_data($get_count, $where);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = "index.php?target=clean_urls_list";

    $smarty->assign('sort_field', "");
    $smarty->assign('sort_direction', 0);

    $orderby = "";
    if (!empty($sort_field)) {
        $orderby = "ORDER BY " . $sort_field;
        $navigation['script'] .= "&sort_field=" . $sort_field;
        $smarty->assign('sort_field', $sort_field);
    }
    if ($sort_direction != "") {
        if (!empty($orderby)) {
            $orderby .= $sort_direction ? " ASC" : " DESC";
        }
        $navigation['script'] .= "&sort_direction=" . $sort_direction;
        $smarty->assign('sort_direction', abs($sort_direction - 1));
    }
    $smarty->assign('navigation', $navigation);

    $get_count = FALSE;
    $limit = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    $url_list = cw_clean_url_get_clean_urls_list_data($get_count, $where, $orderby, $limit);
    $smarty->assign('clean_urls_list', $url_list);

    $smarty->assign('clean_urls_data', $clean_urls_data);
}

if (defined('IS_AJAX')) {
    cw_load('ajax');

    $template = "list_list.tpl";
    $ajax_id = "contents_clean_urls_list";
    if ($mode == "history") {   // for history list tab
        $template = "list_history_list.tpl";
        $ajax_id = "contents_history_clean_urls_list";
    }

    cw_add_ajax_block(array(
        'id' => $ajax_id,
        'action' => 'update',
        'template' => 'addons/clean_urls/' . $template
    ));
}
else {
    if (!empty($top_message)) {
        $smarty->assign('top_message', $top_message);
        $top_message = array();
    }

    $smarty->assign('h_sort_field', "");
    $smarty->assign('h_sort_direction', 0);

    $orderby = "ORDER BY h.ctime";
    $clean_urls_history_list = cw_clean_url_get_clean_urls_history_list_data($orderby);
    $smarty->assign('clean_urls_history_list', $clean_urls_history_list);

    $location[] = array(cw_get_langvar_by_name('lbl_clean_urls_list'), '');

    $smarty->assign('main', 'clean_urls_list');
}
