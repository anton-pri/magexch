<?php
include $app_main_dir.'/include/security.php';

$search = &cw_session_register('search', array());
if (isset($_GET['substring']))
    $search['products_orders']['substring'] = $substring;
if (isset($_GET['fromdate']))
    $search['products_orders']['fromdate'] = cw_core_strtotime($fromdate);
if (isset($_GET['dateto']))
    $search['products_orders']['todate'] = cw_core_strtotime($todate)+86399;
if (isset($_GET['doc_type']))
    $search['products_orders']['doc_type'] = $doc_type;

if ($mode == 'search_orders') {
    $conditions = '';
    $data = $search['products_orders'];

    if ($data['substring'])
        $conditions .= " and (di.product like '%$data[substring]%' or di.productcode like '%$data[substring]%')";
    if ($data['fromdate'])
        $conditions .= " and date >= '$data[fromdate]'";
    if ($data['todate'])
        $conditions .= " and date <= '$data[todate]'";

    $query = "from $tables[docs] as d, $tables[docs_items] as di, $tables[docs_info] as dim where di.doc_id=d.doc_id and d.type='$data[doc_type]' and dim.doc_info_id=d.doc_info_id and di.product_id='$product_id' ".$conditions." group by d.doc_id";
    $_res = db_query("select count(*) $query");
    $total_items = db_num_rows($_res);
    db_free_result($_res);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = "index.php?target=$target&product_id=$product_id";
    $smarty->assign('navigation', $navigation);

    $orders = cw_query("select dim.total, d.type, d.date, d.doc_id, d.display_id $query order by d.date limit $navigation[first_page], $navigation[objects_per_page]");
    $smarty->assign('orders', $orders);
}

$smarty->assign('product_id', $product_id);
$smarty->assign('current_section_dir', 'products');
$smarty->assign('home_style', 'iframe');

$smarty->assign('main', 'orders');
?>
