<?php
if ($action == 'update' && is_array($update_carriers)) {
    foreach($update_carriers as $k=>$v) {
        if ($k == 0) {
            if ($v['carrier']) {
                $v['addon'] = '';
                cw_shipping_insert_carrier($v);
            }
        }
        else 
            cw_shipping_update_carrier($k, $v);
    }
    cw_header_location('index.php?target='.$target);
}

if ($action == 'delete' && is_array($del_carriers)) {
    foreach($del_carriers as $k=>$v)
        cw_shipping_delete_carrier($k);
    cw_header_location('index.php?target='.$target);
}

$carriers = cw_shipping_get_carriers(true);
$smarty->assign('carriers', $carriers);

$location[] = array(cw_get_langvar_by_name('lbl_carriers'), '');
$smarty->assign('current_main_dir', 'addons/shipping_system');
$smarty->assign('current_section_dir', 'admin');
$smarty->assign('main', 'carriers');
