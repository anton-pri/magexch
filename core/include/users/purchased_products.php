<?php
$search = &cw_session_register('search', array());
if (isset($_GET['substring']))
    $search['purchased_products']['substring'] = $substring;
if (isset($_GET['fromdate']))
    $search['purchased_products']['fromdate'] = cw_core_strtotime($fromdate);
if (isset($_GET['todate']))
    $search['purchased_products']['todate'] = cw_core_strtotime($todate);

$conditions = '';
$data = $search['purchased_products'];

if ($data['substring'])
    $conditions .= " and (di.product like '%$data[substring]%' or di.productcode like '%$data[substring]%')";
if ($data['fromdate'])
    $conditions .= " and date >= '$data[fromdate]'";
if ($data['todate'])
    $conditions .= " and date <= '".(intval($data['todate'])+86399)."'";

$query = "from $tables[docs] as d, $tables[docs_items] as di, $tables[docs_user_info] as dui where di.doc_id=d.doc_id and d.type in ('O', 'G', 'I', 'S') and dui.doc_info_id=d.doc_info_id and dui.customer_id='$user' ".$conditions;
$total_items = cw_query_first_cell("select count(*) $query");

$navigation = cw_core_get_navigation($target, $total_items, $page);
$navigation['script'] = "index.php?target=$target&mode=$mode&user=$user";
$smarty->assign('navigation', $navigation);

if ($total_items) {
    $products = cw_query($sql="select di.*, d.date, d.doc_id, d.display_id $query limit $navigation[first_page], $navigation[objects_per_page]");
    $smarty->assign('products', $products);
}

$smarty->assign('user', $user);

$smarty->assign('current_section', '');
$smarty->assign('home_style', 'iframe');
$smarty->assign('main', 'purchased_products');
?>
