<?php
global $docs_type;
$docs_type = 'I';

if ($action == 'add') {
    cw_load('doc');
    $doc_id = cw_doc_create_empty($docs_type.'_');
    cw_header_location("index.php?target=$target&doc_id=$doc_id&mode=edit&new=Y");
}
elseif ($doc_id)
    include $app_main_dir.'/include/orders/order.php';
else
    include $app_main_dir.'/include/orders/orders.php';

$smarty->assign('page_acl', '__20');
