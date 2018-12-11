<?php
$popup_docs = &cw_session_register('popup_docs', array());

if ($target_form) {
    $popup_docs['form'] = $target_form;
    $popup_docs['element_id'] = $element_id;
    $popup_docs['docs_type'] = $docs_type;
}
$target_form = $popup_docs['form'];
$element_id = $popup_docs['element_id'];
$docs_type = $popup_docs['docs_type']?$popup_docs['docs_type']:'O';

include $app_main_dir.'/include/orders/orders.php';

$location[] = array(cw_get_langvar_by_name('lbl_popup_docs'), '');
$location[] = array(cw_get_langvar_by_name('lbl_docs_'.$usertype, ''));

$smarty->assign('target_form', $target_form);
$smarty->assign('element_id', $element_id);
?>
