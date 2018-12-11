<?php
global $docs_type;
$docs_type = 'O';
cw_load('doc');
if ($action == 'add') {
    $doc_id = cw_doc_create_empty($docs_type.'_'); // Create temporary doc_type until first POST request
    cw_header_location("index.php?target=$target&doc_id=$doc_id&mode=edit");
}
elseif ($doc_id) 
    cw_include('include/orders/order.php');
else 
    cw_include('include/orders/orders.php');

$smarty->assign('page_acl', '__18');
?>
