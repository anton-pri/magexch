<?php
cw_load('doc', 'shipping_label');

if (!empty($doc_id)) {
	if (strpos($doc_id, ",") !== false) {
		$tmp = explode(",", $doc_id);
		if ($tmp) {
			foreach ($tmp as $v) {
				$doc_ids[$v] = true;
			}
		}
	} 
    else
		$doc_ids[$doc_id] = true;
}

$up_orders = array();
if ($update === "Y") {
	$up_orders = $doc_ids;
	$doc_ids = array();
	$doc_ids = $doc_ids_all;
}

if (empty($doc_ids)) {
	if ($mode == 'get_label') {
		$top_message['content'] = cw_get_langvar_by_name("lbl_selected_orders_have_no_shipping_labels");
		$top_message['type'] = 'E';
		cw_session_save();
?>
<script type="text/javascript">
<!--
if (window.opener)
	window.opener.history.go(0);
window.close();
-->
</script>
<?php
		exit;
	} else {
		cw_header_location("index.php?target=error_message&error=access_denied&id=42");
	}
}

$all_ups_shipping_labels = array();
$is_first_ups_label = true;
foreach($doc_ids as $id => $v) {
	if ($update != "Y") {
		$e_type = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id = '$id' AND khash = 'shipping_label_error'");
		$l_type = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id = '$id' AND khash = 'shipping_label_type'");
	}
	$is_true = false;
	$order = cw_doc_get($id);
	if (empty($order))
		continue;
	if (($update == "Y") && (!array_key_exists($id, $up_orders)))
		continue;   
	$addon = cw_get_shipping_addon($order['order']['shipping_id']);
	if ((empty($e_type) && empty($l_type) && ($update != "N")) || ($addon == "ups.php")) {
		if (!empty($addon) && file_exists($app_main_dir."/addons/shipping_label_generator/".$addon)) {
			$response = array();
			include $app_main_dir."/addons/shipping_label_generator/".$addon;
			if ($response['result'] != 'ok') {
				db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('$id','shipping_label_error','".$response['error']."')");
				db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('$id','shipping_label_type','')");
				db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('$id','shipping_label','')");
			} else {
				db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('$id','shipping_label','".addslashes($response['image'])."')");
				db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('$id','shipping_label_type','$response[image_type]')");
				db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('$id','shipping_label_error','')");
				$is_true = true;
			}
		}
	} elseif ($type != 'error') {
		$is_true = true;
	}
	$doc_ids[$id] = $is_true;
}

if (!empty($all_ups_shipping_labels)) {
	db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('ups','shipping_label','".addslashes($all_ups_shipping_labels['image'])."')");
	db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('ups','shipping_label_type','$all_ups_shipping_labels[image_type]')");
	db_query("REPLACE INTO $tables[docs_extras] (doc_id,khash,value) VALUES ('ups','shipping_label_error','')");
	$smarty->assign('is_ups_exists', true);
}
if ($REQUEST_METHOD == 'POST') {
	cw_header_location("index.php?target=generator&doc_id=".implode(",", array_keys($doc_ids)));
}

#
# Get label
#
if ($mode == 'get_label' && $doc_ids) {
	foreach ($doc_ids as $id => $v) {
		if (!$v) {
			unset($doc_ids[$id]);
		} else {
			$lable_type = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id = '$v[doc_id]' AND khash = 'shipping_label_type'");
			if (strpos($lable_type, "image/") != 0) {
				unset($doc_ids[$id]);	
			}
		}
	}
	if (empty($doc_ids)) {
		$top_message['content'] = cw_get_langvar_by_name("lbl_selected_orders_have_no_shipping_labels");
		$top_message['type'] = 'E';
		cw_session_save();
		echo "<script>if(window.opener) window.opener.history.go(0); window.close();</script>";
		exit;
	}
	$smarty->assign('doc_ids', array_keys($doc_ids));
	cw_display("addons/shipping_label_generator/labels.tpl", $smarty);
	exit;
}

if ($doc_ids) {
	$orders = cw_query("select d.*, s.* from $tables[docs] as d, $tables[docs_info] as di LEFT JOIN $tables[shipping] as s ON di.shipping_id = s.shipping_id where d.doc_id in ('".implode("','", array_keys($doc_ids))."') and d.doc_info_id=di.doc_info_id");

	if (empty($orders))
		cw_header_location("index.php?target=error_message&error=access_denied&id=49");

	$is_sl_i_type = false;
	foreach ($orders as $k => $v) {
		$orders[$k]['shipping_label_type'] = $v['shipping_label_type'] = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id = '$v[doc_id]' AND khash = 'shipping_label_type'");

		$orders[$k]['shipping_label_error'] = $v['shipping_label_error'] = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id = '$v[doc_id]' AND khash = 'shipping_label_error'");
		$is_image = 'N';
		if (strpos($v['shipping_label_type'], "image/") === 0) {
			$orders[$k]['sl_type'] = 'I';
			$is_sl_i_type = 'Y';
			$is_image = 'Y';
		} elseif (empty($v['shipping_label_type']) && (!empty($v['shipping_label_error']))) {
			$orders[$k]['sl_type'] = 'E';
		} elseif (!empty($v['shipping_label_type'])) {
			$orders[$k]['sl_type'] = 'D';
		} else {
			$orders[$k]['sl_type'] = 'N';
		}
	}
	$smarty->assign('is_image', $is_image);
	$smarty->assign('orders', $orders);
	$smarty->assign('is_sl_i_type', $is_sl_i_type);
}

$smarty->assign('main', 'slg');
?>
