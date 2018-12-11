<?php
$controller_target = 'breadcrumbs_management';

$breadcrumb_session_data = &cw_session_register('breadcrumb_session_data', array());
$top_message = &cw_session_register('top_message');

if ($action == 'delete' && !empty($delete_breadcrumb)) {

    foreach ($delete_breadcrumb as $breadcrumb_id => $_v) {
        cw_breadcrumbs_delete_breadcrumb($breadcrumb_id);
    }
    $top_message = array('content' => cw_get_langvar_by_name('txt_selected_breadcrumbs_deleted'), 'type' => 'I');
    cw_header_location("index.php?target=$controller_target&action=process");
}

if ($action == 'add_breadcrumb' || $action == 'edit_breadcrumb') {
    $b_link = trim($breadcrumb_new['link']);
    $b_title = trim($breadcrumb_new['title']);
    $b_uniting = isset($breadcrumb_new['uniting']) ? 1 : 0;
    $b_parent_id = intval(trim($breadcrumb_new['parent_id']));
    if ($b_parent_id == -1) $b_parent_id = 0;
    $b_area = trim($breadcrumb_new['area']);

    if (
        !empty($b_link)
        && !empty($b_title)
    ) {
        $b_link = '/' . ltrim($b_link, '/');
        $b_breadcrumb_id = trim($breadcrumb_new['breadcrumb_id']);

        if (cw_breadcrumbs_is_unique_link($b_link, $action, $b_breadcrumb_id)) {

            if ($action == 'add_breadcrumb') {
                cw_array2insert(
                    'breadcrumbs',
                    array(
                        'link' => $b_link,
                        'title' => $b_title,
                        'uniting' => $b_uniting,
                        'parent_id' => $b_parent_id,
                        'area' => $b_area,
                    )
                );
                $top_message = array('content' => cw_get_langvar_by_name('msg_breadcrumb_added'), 'type' => 'I');
            }
            else {
                cw_array2update(
                    'breadcrumbs',
                    array(
                        'link' => $b_link,
                        'title' => $b_title,
                        'uniting' => $b_uniting,
                        'parent_id' => $b_parent_id,
                        'area' => $b_area,
                    ),
                    "breadcrumb_id = $b_breadcrumb_id"
                );
            }
            $top_message = array('content' => cw_get_langvar_by_name('msg_breadcrumb_updated'), 'type' => 'I');
        }
        else {
            $top_message = array('content' => cw_get_langvar_by_name('msg_err_link_already_exists'), 'type' => 'E');
        }
    }
    else {
        $top_message = array('content' => cw_get_langvar_by_name('lbl_please_fill_required_fields'), 'type' => 'E');
    }
    cw_header_location("index.php?target=$controller_target&action=process");
}

$breadcrumb_data['page'] = $breadcrumb_session_data['page'] = (!empty($page) ? $page : 1);
$breadcrumb_data['sort_field'] = "";
$breadcrumb_data['sort_direction'] = "";
$where = "";
$orderby = "";
$limit = "";

if ($action == 'reset') {
    $breadcrumb_session_data['search'] = array();
    cw_header_location("index.php?target=" . $controller_target);
}

if ($action == "search") {
    $breadcrumb_session_data = $breadcrumb_data;
    $where = cw_breadcrumbs_generate_where_search($breadcrumb_data);
}

if ($action == "process") {
    $breadcrumb_data = $breadcrumb_session_data;
    $where = cw_breadcrumbs_generate_where_search($breadcrumb_data);

    $avail_sort_fields = array('link', 'title','parent_link','area');

    if (in_array($sort, $avail_sort_fields)) {
        $orderby = "ORDER BY b1." . $sort;

        if ($direction == 1) {
            $orderby .= " DESC";
        }
        $breadcrumb_data['sort_field'] = $sort;
        $breadcrumb_data['sort_direction'] = $direction;
    }
}

$total_items = cw_breadcrumbs_get_management_breadcrumbs($where, $orderby, "", TRUE);

$navigation = cw_core_get_navigation($target, $total_items, $page);
$navigation['script'] = "index.php?target=$controller_target&action=process";
if (!empty($breadcrumb_data['sort_field'])) {
    $navigation['script'] .= "&sort=" . $breadcrumb_data['sort_field'];
}
if ($breadcrumb_data['sort_direction'] != "") {
    $navigation['script'] .= "&direction=" . $breadcrumb_data['sort_direction'];
}
$smarty->assign('navigation', $navigation);

$limit = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
$breadcrumbs = cw_breadcrumbs_get_management_breadcrumbs($where, $orderby, $limit);

$smarty->assign('breadcrumb_areas', cw_query_column("SELECT DISTINCT area FROM $tables[breadcrumbs] ORDER BY area"));

$smarty->assign('breadcrumbs', $breadcrumbs);
$smarty->assign('breadcrumb_data', $breadcrumb_data);

$smarty->assign('target_breadcrumbs_management', 'index.php?target=' . $controller_target);

$smarty->assign('current_section_dir', 'main');
$smarty->assign('main', 'breadcrumbs_management');
