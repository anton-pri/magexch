<?php
if ($action == 'ajax_update') {
    $smarty->assign('cod_types', cw_shipping_get_cod_types());
    $smarty->assign('user_selection', cw_query_first_cell("select cod_delivery_type_id from $tables[customers_customer_info] where customer_id='$user'"));

    cw_display('admin/shipping/cod_types_ajax_js.tpl', $smarty);
    exit(0);
}

if ($action == 'update' && is_array($update_types)) {
    foreach($update_types as $k=>$v) {
        if ($k) $v['cod_type_id'] = $k;
        if (!$k && !$v['title']) continue;
        cw_array2insert('shipping_cod_types', $v, true);
    }
    cw_header_location("index.php?target=$target&js_update=1".($iframe?'&iframe=1':'')); 
}

if ($action == 'delete' && is_array($del)) {
    foreach($del as $cod_type_id=>$val)
        cw_shipping_delete_cod_type($cod_type_id);
    cw_header_location("index.php?target=$target&js_update=1".($iframe?'&iframe=1':''));
}

$smarty->assign('js_update', $js_update&&$iframe);
$smarty->assign('cod_types', cw_shipping_get_cod_types());
$location[] = array(cw_get_langvar_by_name('lbl_cod_types'), '');
$smarty->assign('main', 'cod_types');

if ($iframe) {
    $smarty->assign('current_section', '');
    $smarty->assign('home_style', 'iframe');
    $smarty->assign('iframe', $iframe);
}

$smarty->assign('current_main_dir', 'addons/shipping_system');
$smarty->assign('current_section_dir', 'admin');
