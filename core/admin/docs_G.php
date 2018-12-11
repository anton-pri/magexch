<?php
# kornev, TOFIX
if (!$addons['pos']) cw_header_location('index.php');

$docs_type = 'G';

if ($action == 'add') {
    cw_load('doc');
    $doc_id = cw_doc_create_empty($docs_type);
    cw_header_location("index.php?target=$target&doc_id=$doc_id&mode=edit");
}
elseif ($doc_id) 
    include $app_main_dir.'/include/orders/order.php';
else
    include $app_main_dir.'/include/orders/orders.php';
$smarty->assign('page_acl', '__0700');
