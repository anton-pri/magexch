<?php
cw_load('doc', 'mail');
$orders_to_delete = &cw_session_register('orders_to_delete', array());

if ($action == 'mass_update') {
    $flag = 0;
    define('ORDERS_LIST_UPDATE', 1);

    if (is_array($doc_ids) && is_array($order_status_old)) {  
        foreach($doc_ids as $orderid => $_on) { 
            if (!isset($order_status_old[$orderid])) continue;   

            if ($mass_update_order_status != $order_status_old[$orderid]) {
                cw_call('cw_doc_change_status', array($orderid, $mass_update_order_status));
                $flag = 1;
            }  
        }
    }

    if ($flag)
        $top_message["content"] .= cw_get_langvar_by_name("msg_adm_orders_upd");
    cw_header_location("index.php?target=$target&mode=search");
} 

if ($action == 'update') {
    $flag = 0;
    define('ORDERS_LIST_UPDATE', 1);

    if (is_array($order_status) && is_array($order_status_old))
    foreach($order_status as $orderid=>$status) {
        if (is_numeric($orderid) && $status != $order_status_old[$orderid]) {
            cw_call('cw_doc_change_status', array($orderid, $status));
            $flag = 1;
        }
    }
    if ($flag)
        $top_message["content"] .= cw_get_langvar_by_name("msg_adm_orders_upd");
    cw_header_location("index.php?target=$target&mode=search");
}

if ($action == 'delete' || $action == 'delete_all') {
    if ($confirmed == 'Y') {
        if ($action == 'delete_all') {
            cw_doc_delete_all();
			$top_message = array('content' => cw_get_langvar_by_name('msg_adm_all_orders_del'));
			cw_header_location("index.php?target=$target&mode=search");
        }

        if (is_array($orders_to_delete)) {
            foreach ($orders_to_delete as $k=>$v)
                $res &= cw_func_call('cw_doc_delete', $k);
            $orders_to_delete = '';

            $top_message['content'] = cw_get_langvar_by_name('lbl_docs_deleted');
            cw_header_location("index.php?target=$target&mode=search");
	    }
	}
    else {
		$orders_to_delete = $doc_ids;
        cw_header_location("index.php?target=$target&mode=".$action);
    }
}

if ($mode == 'delete_all' || $mode == 'delete') {
	$location[] = array(cw_get_langvar_by_name('lbl_orders_management'), '');
	$location[] = array(cw_get_langvar_by_name('lbl_docs_info_'.$docs_type), '');

    if (is_array($orders_to_delete))
        $smarty->assign('docs', cw_query("select * from $tables[docs] where doc_id in ('".implode("', '", array_keys($orders_to_delete))."')"));

	$smarty->assign('mode', $mode);
    $smarty->assign('orders_count', count($orders_to_delete));
	$smarty->assign('main', 'delete_confirmation');

	cw_display("admin/index.tpl",$smarty);
	exit;
}

unset($orders_to_delete);
$top_message = array('content' => cw_get_langvar_by_name('msg_adm_warn_orders_sel'), 'type' => 'W');

cw_header_location("index.php?target=$target&mode=search");
?>
