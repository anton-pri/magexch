<?php
$search_data = &cw_session_register('search_data', array());

$docs_type = 'O';

if ($action == 'add') {
    cw_load('doc');
    $doc_id = cw_doc_create_empty($docs_type);
    cw_header_location('index.php?target=order&doc_id='.$doc_id);
}

if ($action == 'delete_all')
    include $app_main_dir.'/include/process_order.php';

$docs_type = 'O';
include $app_main_dir.'/include/orders/orders.php';
