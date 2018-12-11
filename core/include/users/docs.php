<?php
if ($action == 'generate_group') {
    cw_load('doc');
    cw_doc_generate_group(array_keys($docs));
    cw_header_location("index.php?target=$target&mode=$mode&user=$user");
}

$search = &cw_session_register('search');
if (isset($_GET['doc_type']))
    $search['placed_docs']['doc_type'] = $doc_type;
if (isset($_GET['fromdate'])) {
    if (empty($fromdate)) $search['placed_docs']['basic']['creation_date_start'] = 0;
    else $search['placed_docs']['basic']['creation_date_start'] = cw_core_strtotime($fromdate);
}
if (isset($_GET['todate'])) {
    if (empty($fromdate)) $search['placed_docs']['basic']['creation_date_end'] = 0;
    else $search['placed_docs']['basic']['creation_date_end']  = cw_core_strtotime($todate)+86399;
}

$search['placed_docs']['basic']['customer_id'] = $user;

$_tmp_cond = $search_data['orders'];
if ($search['placed_docs']['doc_type']) $docs_type = $search['placed_docs']['doc_type'];
else $docs_type = 'O';

$search_data['orders'][$docs_type] = $search['placed_docs'];

$mode = 'search';
include $app_main_dir.'/include/orders/orders.php';
$mode = 'docs';

$search['placed_docs'] = $search_data['orders'][$docs_type];
$search_data['orders'] = $_tmp_cond;

$smarty->assign('user', $user);
$smarty->assign('docs_type', $docs_type);

$smarty->assign('current_section', '');
$smarty->assign('main', 'docs'); 
$smarty->assign('home_style', 'iframe');

?>
