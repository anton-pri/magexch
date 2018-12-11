<?php
# kornev, TOFIX
if (!$addons['Salesman'])
	cw_header_location("index.php?target=error_message&error=access_denied&id=17");

if ($StartDay)
	$search['start_date'] = mktime(0, 0, 0, $StartMonth, $StartDay, $StartYear);

if ($EndDay)
	$search['end_date'] = mktime(23, 59, 59, $EndMonth, $EndDay, $EndYear);

if ($search) {
	$where = array();
	if ($search['campaign_id'])
		$where[] = "$tables[salesman_adv_campaigns].campaign_id = '$search[campaign_id]'";

	if ($search['start_date'] && $search['end_date']) {
		if($search['end_date'] < $search['start_date']) {
			$tmp = $search['end_date'];
			$search['end_date'] = $search['start_date'];
			$search['start_date'] = $tmp;
		}

		$where[] = " ($tables[salesman_adv_campaigns].start_period BETWEEN $search[start_date] AND $search[end_date] OR $tables[salesman_adv_campaigns].end_period BETWEEN $search[start_date] AND $search[end_date] OR $search[start_date] BETWEEN $tables[salesman_adv_campaigns].start_period AND $tables[salesman_adv_campaigns].end_period OR $search[end_date] BETWEEN $tables[salesman_adv_campaigns].start_period AND $tables[salesman_adv_campaigns].end_period)";
	}

	if ($where)
		$where = " WHERE ".implode(" AND ", $where);

	$result = cw_query("SELECT $tables[salesman_adv_campaigns].*, COUNT($tables[salesman_adv_clicks].add_date) as clicks FROM $tables[salesman_adv_campaigns] LEFT JOIN $tables[salesman_adv_clicks] ON $search[end_date] > $tables[salesman_adv_clicks].add_date AND $tables[salesman_adv_clicks].add_date > $search[start_date] AND $tables[salesman_adv_campaigns].campaign_id = $tables[salesman_adv_clicks].campaign_id $where GROUP BY $tables[salesman_adv_campaigns].campaign_id");
	if ($result) {
		$total = array();
		foreach ($result as $k => $v) {
			$start = (($v['start_period'] > $search['start_date'])?$v['start_period']:$search['start_date']);
			$end = (($v['end_period'] < $search['end_date'])?$v['end_period']:$search['end_date']);

			$per_day = $v['per_period']/ceil(abs($v['end_period']-$v['start_period'])/86400);
			$v['ee'] = round(($v['per_visit']*$v['clicks']) + $per_day*ceil(abs($end-$start)/86400), 2);
			$tmp = cw_query_first("SELECT SUM($tables[orders].total) as sum, COUNT($tables[orders].total) as cnt FROM $tables[orders], $tables[salesman_adv_orders] WHERE $tables[salesman_adv_orders].campaign_id = '$v[campaign_id]' AND $tables[salesman_adv_orders].doc_id = $tables[orders].doc_id AND $tables[orders].status IN ('P', 'C') AND $tables[orders].date BETWEEN $start AND $end");
			if($v['ee'] > 0 && $tmp['cnt'] > 0) {
				$v['acost'] = $v['ee']/$tmp['cnt'];
			} else {
				$v['acost'] = 0;
			}

			$v['total'] = $tmp['sum'];
			if($v['total'] > 0 && $v['ee'] > 0) {
				$v['roi'] = round($v['total']/$v['ee']*100, 2);
			} else {
				$v['roi'] = 0;
			}

			$total['clicks'] += $v['clicks'];
			$total['ee'] += $v['ee'];
			$total['acost'] += $v['acost'];
			$total['total'] += $v['total'];
			$result[$k] = $v;
		}

		$total['roi'] = 0;
		if ($total['total'] > 0 && $total['ee'] > 0) {
			$total['roi'] = round($total['total']/$total['ee']*100, 2);
		}

		$smarty->assign('total', $total);
		$smarty->assign('result', $result);
	}
}

$campaigns = cw_query("SELECT * FROM $tables[salesman_adv_campaigns]");
$smarty->assign('campaigns', $campaigns);
$smarty->assign('search', $search);
$smarty->assign ("month_begin", mktime(0,0,0,date('m'),1,date('Y')));

$smarty->assign ("main", "salesman_adv_stats");
