<?php
$controller_target = 'select_breadcrumb';

$s_breadcrumb_session_data = &cw_session_register('s_breadcrumb_session_data', array());

$breadcrumb_data['page'] = $s_breadcrumb_session_data['page'] = (!empty($page) ? $page : 1);
$breadcrumb_data['sort_field'] = "";
$breadcrumb_data['sort_direction'] = "";
$where = " AND b1.parent_id >= 0";
$mandatory_where = array("b1.parent_id >= 0");
$orderby = "";
$limit = "";

if ($action == 'reset') {
    $s_breadcrumb_session_data['search'] = array();
    cw_header_location("index.php?target=" . $controller_target);
}

if ($action == "search") {
    $s_breadcrumb_session_data = $breadcrumb_data;
    $where = cw_breadcrumbs_generate_where_search($breadcrumb_data, $mandatory_where);
}

if ($action == "process") {
    $breadcrumb_data = $s_breadcrumb_session_data;
    $where = cw_breadcrumbs_generate_where_search($breadcrumb_data, $mandatory_where);

    $avail_sort_fields = array(
        'link' => 'b1.link',
        'title' => 'b1.title',
        'parent_link' => 'parent_link'
    );

    if (array_key_exists($sort, $avail_sort_fields)) {
        $orderby = "ORDER BY " . $avail_sort_fields[$sort];

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

$smarty->assign('breadcrumbs', $breadcrumbs);
$smarty->assign('breadcrumb_data', $breadcrumb_data);

$smarty->assign('target_select_breadcrumb', 'index.php?target=' . $controller_target);

$smarty->assign('current_section_dir', 'main');
$smarty->assign('main', 'select_breadcrumb');
$smarty->assign('home_style', 'popup');
