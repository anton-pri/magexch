<?php
if (!defined('APP_START')) die('Access denied');
// artem, TODO: this script uses non-existing tables. Review where it is used - delete or fix
function cw_sales_sort_callback($a, $b) {
	global $search;

	if (!isset($a[$search['sort_by']]) || !isset($b[$search['sort_by']]) || $a[$search['sort_by']] == $b[$search['sort_by']])
		return 0;

	return ($a[$search['sort_by']] < $b[$search['sort_by']]) ? -1 : 1;
}

if ($StartDay)
	$search['start_date'] = mktime(0, 0, 0, $StartMonth, $StartDay, $StartYear);

if ($EndDay)
	$search['end_date'] = mktime(23, 59, 59, $EndMonth, $EndDay, $EndYear);

if ($search) {
	$where = array();

	if ($search['salesman'])
		$where[] = "$tables[customers].customer_id='$search[salesman]'";

	if ($search['productcode'])
		$where[] = "$tables[products].productcode = '$search[productcode]'";

	if ($search['status'])
		$where[] = "$tables[salesman_payment].paid = '$search[status]'";

	if ($search['start_date'] && $search['end_date'])
		$where[] = $search['end_date']." > $tables[salesman_payment].add_date AND $tables[salesman_payment].add_date > ".$search['start_date'];

	if ($current_area == 'B')
		$where[] = "$tables[salesman_payment].salesman_customer_id='$customer_id'";
	else
		$where[] = "$tables[salesman_payment].affiliate = ''";

	if (!$search['sort_by'])
		$search['sort_by'] = 'total';

	if ($where)
		$where_condition = " AND ".implode(" AND ", $where);

	$sales = array();
	if ($search['top']) {
		$res = db_query($sql="SELECT $tables[products].product, $tables[products].product_id, $tables[salesman_payment].commissions,  $tables[order_details].amount, $tables[salesman_product_commissions].product_commission, $tables[order_details].extra_data FROM $tables[customers], $tables[salesman_payment], $tables[order_details], $tables[products], $tables[orders], $tables[salesman_product_commissions] WHERE $tables[salesman_product_commissions].item_id = $tables[order_details].item_id AND $tables[salesman_product_commissions].doc_id = $tables[order_details].doc_id AND $tables[salesman_product_commissions].customer_id=$tables[customers].customer_id AND $tables[customers].usertype = 'B' AND $tables[customers].status = 'Y' AND $tables[salesman_payment].customer_id=$tables[customers].customer_id AND $tables[salesman_payment].doc_id = $tables[order_details].doc_id AND $tables[orders].doc_id = $tables[order_details].doc_id AND $tables[order_details].product_id = $tables[products].product_id ".$where_condition);

		if ($res) {
			while ($row = db_fetch_array($res)) {
				$row['total'] = 0;
				if (!empty($row['extra_data'])) {
					$row['extra_data'] = unserialize($row['extra_data']);
					if (is_array($row['extra_data']))
						$row['total'] = $row['extra_data']['display']['discounted_price'];
				}

				if (!isset($sales[$row['product_id']])) {
					$sales[$row['product_id']] = $row;
					$sales[$row['product_id']]['sales'] = 1;
					continue;
				}

				$sales[$row['product_id']]['commissions'] += $row['commissions'];
				$sales[$row['product_id']]['amount'] += $row['amount'];
				$sales[$row['product_id']]['product_commission'] += $row['product_commission'];
				$sales[$row['product_id']]['total'] += $row['total'];
				$sales[$row['product_id']]['sales']++;
			}
			db_free_result($res);
			usort($sales, "cw_sales_sort_callback");
		}

	} else {
		$sales = cw_query("SELECT $tables[salesman_product_commissions].product_commission, $tables[salesman_payment].*, $tables[customers].*, $tables[docs_items].*, $tables[products].product, $tables[products].product_id, $tables[docs_items].extra_data, $tables[docs].display_id FROM $tables[customers], $tables[salesman_payment], $tables[docs_items], $tables[docs_items_relation], $tables[products], $tables[docs], $tables[salesman_product_commissions] WHERE $tables[salesman_product_commissions].item_id = $tables[docs_items].item_id AND $tables[salesman_product_commissions].doc_id = $tables[docs].doc_id AND $tables[salesman_product_commissions].salesman_customer_id=$tables[customers].customer_id AND $tables[customers].usertype = 'B' AND $tables[customers].status = 'Y' AND $tables[salesman_payment].salesman_customer_id=$tables[customers].customer_id AND $tables[salesman_payment].doc_id = $tables[docs].doc_id AND $tables[docs].doc_id = $tables[docs_items_relation].doc_id and $tables[docs_items].item_id=$tables[docs_items_relation].item_id AND $tables[docs_items].product_id = $tables[products].product_id ".$where_condition." GROUP BY $tables[salesman_payment].payment_id, $tables[docs_items].item_id");
		if (!empty($sales)) {
			foreach ($sales as $k => $v) {
				$sales[$k]['total'] = 0;
				if (!empty($v['extra_data'])) {
					$v['extra_data'] = unserialize($v['extra_data']);
					if (is_array($v['extra_data']))
						$sales[$k]['total'] = $v['extra_data']['display']['discounted_price'];
				}
			}
		}

	}

	if ($current_area == 'B' && $sales && $search['top'] != 'Y') {
		$parent_pending = 0;
		$parent_paid = 0;
		foreach ($sales as $k => $v) {
			if (!empty($v['affiliate'])) {
				if ($v['paid'] == 'Y')
					$parent_paid += $v['product_commission'];
				else
					$parent_pending += $v['product_commission'];
//				unset($sales[$k]);

			}
		}
		$smarty->assign('parent_pending', $parent_pending);
		$smarty->assign('parent_paid', $parent_paid);
	}
	$smarty->assign('sales', $sales);
}

$smarty->assign('salesmans', cw_query("SELECT * FROM $tables[customers] WHERE usertype = 'B' AND status = 'Y'"));

$smarty->assign('search', $search);

$smarty->assign('month_begin', mktime(0,0,0,date('m'),1,date('Y')));
?>
