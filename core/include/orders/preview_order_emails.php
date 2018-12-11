<?php
cw_load( 'doc');

$allowed_order_status = cw_doc_get_allowed_statuses();

/*
if (!in_array($status_code, $allowed_order_status) || empty($status_code)) 
    cw_close_window();
*/

$order_status_data = cw_query_first("select * from $tables[order_statuses] where code='$status_code'");

if (!$doc_id)
    $doc_id = cw_query_first_cell("select doc_id from $tables[docs] order by doc_id desc limit 1");

$doc_data = cw_doc_get($doc_id, 8192);

if ($status_code)
    $doc_data['status'] = $status_code;

cw_load('web');
if ($doc_data['info']['layout_id'])
    $layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
else
    $layout = cw_web_get_layout('docs_'.$doc_data['type']);

if ($preview_area == 'admin') 
    $smarty->assign('usertype_layout', 'A');
elseif ($preview_area == 'seller')
    $smarty->assign('usertype_layout', 'V');
else 
    $smarty->assign('usertype_layout', '');


$smarty->assign('is_email_invoice', 'Y');
$smarty->assign('product_layout_elements',cw_call('cw_web_get_product_layout_elements', array()));
$smarty->assign('preview_area', $preview_area);
$smarty->assign('layout_data', $layout);
$smarty->assign('info', $doc_data['info']);
$smarty->assign('products', $doc_data['products']);
$smarty->assign('order', $doc_data);
$smarty->assign('doc', $doc_data);
$smarty->assign('is_email_invoice', 'Y');
$smarty->assign('main', 'preview_order_emails');
$smarty->assign('home_style', 'iframe');
define('PREVENT_XML_OUT', true); // need simple HTML out if controller called as ajax via $.load()
cw_call('cw_md_send_mail');
?>
