<?php
global $smarty;

if ($mode == "delete_from_stop_list" && is_numeric($review_id)) {
    cw_review_delete_from_stop_list($review_id);
    $stop_list = cw_review_get_stop_list();
    $smarty->assign('stop_list', $stop_list);

    cw_add_ajax_block(array(
        'id' 		=> 'estore_container_id',
        'action' 	=> 'update',
        'template' 	=> 'addons/estore_products_review/admin_stop_list_item.tpl'
    ));
}
else {
    $stop_list = cw_review_get_stop_list();

    $smarty->assign('stop_list', $stop_list);
    $smarty->assign('current_section_dir', 'main');
    $smarty->assign('main', 'estore_stop_list');
}