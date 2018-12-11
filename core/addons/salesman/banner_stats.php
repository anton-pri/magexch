<?php
if (!defined('APP_START')) die('Access denied');

#
# Callback function for banners list sorting
#
function cw_banners_sort($a, $b) {
	if ($a['banner_id'] == $b['banner_id']) {
		if ($a['product'] == $b['product']) {
			if ($a['class'] == $b['class'])
				return 0;

			return $a['class'] > $b['class'] ? 1 : -1;
		}

		return $a['product'] > $b['product'] ? 1 : -1;
	}

	return $a['banner_id'] > $b['banner_id'] ? 1 : -1;
}

if ($posted_data) {
    $date_fields = array (
        '' =>array('start_date' => 0, 'end_date' => 1),
    );
    cw_core_process_date_fields($posted_data, $date_fields);
    $search = $posted_data;
}

if ($search) {
	if($current_area == 'B')
		$search['salesman'] = $customer_id;

	$where = array();
	$where_clicks = array();

	if ($search['start_date'] && $search['end_date']) {
		$where[] = "$search[end_date] > $tables[salesman_views].add_date AND $tables[salesman_views].add_date > $search[start_date]";
		$where_clicks[] = "$search[end_date] > $tables[salesman_clicks].add_date AND $tables[salesman_clicks].add_date > $search[start_date]";
	}

	if ($search['salesman']) {
		$where[] = "$tables[salesman_views].salesman_customer_id='$search[salesman]'";
		$where_clicks[] = "$tables[salesman_clicks].salesman_customer_id='$search[salesman]'";
	}

    $where[] = "$tables[salesman_views].banner_id is not null";
    $where_clicks[] = "$tables[salesman_clicks].banner_id is not null";

	$where_condition = implode(" AND ", $where);
	$where_clicks_condition = implode(" AND ", $where_clicks);

	$views = cw_query("SELECT $tables[salesman_banners].banner_id, $tables[salesman_banners].banner, $tables[salesman_views].product_id, $tables[salesman_views].class, COUNT($tables[salesman_views].banner_id) as views FROM $tables[salesman_banners] LEFT JOIN $tables[salesman_views] ON $tables[salesman_banners].banner_id = $tables[salesman_views].banner_id where $where_condition GROUP BY $tables[salesman_banners].banner_id, $tables[salesman_views].product_id, $tables[salesman_views].class");
	$clicks = cw_query("SELECT $tables[salesman_banners].banner_id, $tables[salesman_banners].banner, $tables[salesman_clicks].product_id, $tables[salesman_clicks].class, COUNT($tables[salesman_clicks].banner_id) as clicks FROM $tables[salesman_banners] LEFT JOIN $tables[salesman_clicks] ON $tables[salesman_banners].banner_id = $tables[salesman_clicks].banner_id where $where_clicks_condition group by $tables[salesman_banners].banner_id, $tables[salesman_clicks].product_id, $tables[salesman_clicks].class");

	if (!empty($views) || !empty($clicks)) {
		$banners = array();
		$total = array();

		$is_empty = false;
		if (empty($views)) {
			$tmp = $clicks;
			$is_empty = 'V';

		} elseif (empty($clicks)) {
			$tmp = $views;
			$is_empty = 'C';

		} else {
			$tmp = cw_array_merge($views, $clicks);
		}
		
		$len = count($tmp);
		for($k = 0; $k < $len; $k++) {
			if (empty($tmp[$k]))
				continue;

			$v = $tmp[$k];

			if ($v['product_id'])
				$v['product'] = cw_query_first_cell("SELECT product FROM $tables[products] WHERE product_id = '$v[product_id]'");

			if ($is_empty == 'V') {
				$v['views'] = 0;
				$v['click_rate'] = 0;

			} elseif ($is_empty == 'C') {
				$v['clicks'] = 0;
				$v['click_rate'] = 0;

			} else {

				for ($i = $k+1; $i < $len; $i++) {
					if (
						empty($tmp[$i]) || 
						$tmp[$i]['banner_id'] != $v['banner_id'] ||
						$tmp[$i]['product_id'] != $v['product_id'] ||
						$tmp[$i]['class'] != $v['class']
					)
						continue;

					if (!isset($v['clicks'])) {
						$v['clicks'] = $tmp[$i]['clicks'];
					} else {
						$v['views'] = $tmp[$i]['views'];
					}
					$tmp[$i] = false;
					break;
				}

				if (!isset($v['clicks']))
					$v['clicks'] = 0;
				if (!isset($v['views']))
					$v['views'] = 0;

				if ($v['clicks'] == 0 || $v['views'] == 0) {
					$v['click_rate'] = 0;
				} else {
					$v['click_rate'] = round($v['clicks']/$v['views'], 2);
				}
			}

			$total['clicks'] += $v['clicks'];
			$total['views'] += $v['views'];

			$banners[] = $v;
		}

		unset($tmp, $views, $clicks, $len);

		if ($total['clicks'] == 0 || $total['views'] == 0) {
			$total['click_rate'] = 0;
		} else {
			$total['click_rate'] = round($total['clicks']/$total['views'], 2);
		}

		usort($banners, "cw_banners_sort");
		$smarty->assign ("banners", $banners);
		$smarty->assign ("total", $total);
	}
}

$smarty->assign ("salesmans", cw_query("SELECT * FROM $tables[customers] WHERE usertype = 'B' AND status = 'Y'"));

$smarty->assign ("search", $search);
$smarty->assign ("month_begin", mktime(0,0,0,date('m'),1,date('Y')));

?>
