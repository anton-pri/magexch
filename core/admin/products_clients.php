<?php
$search = &cw_session_register('search', array());
if (isset($_GET['substring']))
    $search['products_clients']['substring'] = $substring;
if (isset($_GET['fromdate']))
    $search['products_clients']['fromdate'] = cw_core_strtotime($fromdate);
if (isset($_GET['dateto']))
    $search['products_clients']['todate'] = cw_core_strtotime($todate)+86399;

if ($mode == 'search_clients') {
    $data = $search['products_clients'];

    $where = array();
    $from_tbls = array();
    $query_joins = array();
    $fields = array("$tables[docs_user_info].customer_id", "$tables[docs].doc_id", 'display_id', '`date`', 'amount', 'price', 'usertype');

    if ($data['fromdate'])
        $where[] = "`date` >= '$data[fromdate]'";
    if ($data['todate'])
        $where[] = "`date` <= '$data[todate]'";

    $from_tbls[] = 'docs_items';

    $query_joins['docs'] = array(
        'parent' => 'docs_items',
        'on' => "$tables[docs].type in ('O', 'I', 'S') and $tables[docs].doc_id=$tables[docs_items].doc_id",
    );
    $query_joins['docs_user_info'] = array(
        'parent' => 'docs',
        'on' => "$tables[docs_user_info].doc_info_id = $tables[docs].doc_info_id",
    );

    if ($data['substring']) {
        $add_conditions = array("$tables[docs_user_info].email like '%$data[substring]%'");
        foreach(array('firstname', 'lastname') as $field)
            $add_conditions[] = "$tables[customers_addresses].$field like '%$data[substring]%'";
        $where[] = '('.implode(' or ', $add_conditions).')';

        $query_joins['customers_addresses'] = array(
            'parent' => 'docs_user_info',
            'on' => "$tables[customers_addresses].address_id = $tables[docs_user_info].current_address_id or $tables[customers_addresses].address_id = $tables[docs_user_info].main_address_id",
            'pos' => 10,
        );
    }

    $where[] = "$tables[docs_items].product_id='$product_id'";

    $groupbys = array("$tables[docs_user_info].customer_id");
    $orderbys = array("`$tables[docs]`.`date`");

    $search_query_count = cw_db_generate_query('count(*)',  $from_tbls, $query_joins, $where, $groupbys, array(), array(), 0);
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, array(), $orderbys);

    $_res = db_query($search_query_count);
    $total_items = db_num_rows($_res);
    db_free_result($_res);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = "index.php?target=$target&product_id=$product_id&mode=search_clients";
    $smarty->assign('navigation', $navigation);

    $products = cw_query($search_query." limit $navigation[first_page], $navigation[objects_per_page]");
    $smarty->assign('products', $products);
}

$smarty->assign('product_id', $product_id);
$smarty->assign('current_section_dir', 'products');
$smarty->assign('home_style', 'iframe');

$smarty->assign('main', 'customers');
?>
