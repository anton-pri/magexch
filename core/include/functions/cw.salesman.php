<?php
function cw_salesman_get_target($salesman) {
    global $tables;
    return cw_query_first("select * from $tables[salesman_target] where salesman_customer_id='$salesman'");
}

function cw_salesman_current_level($salesman) {
    global $tables;

    $target = cw_salesman_get_target($salesman);
    if ($target)
        return (float) cw_query_first_cell("select sum(subtotal) from $tables[docs] as d, $tables[docs_info] as di where di.doc_info_id=d.doc_info_id and d.status in ('C', 'P') and salesman_customer_id='$salesman' and date >= '$target[start_date]' and date <= '$target[end_date]'");
    return 0;
}

function cw_salesman_is_reached($salesman) {
    $current_level = cw_salesman_current_level($salesman);
    $target = cw_salesman_get_target($salesman);
    if ($target['target'] > 0 and $target['target'] <= $current_level) return true;
    return false;
}

function cw_salesman_is_selected($salesman) {
    global $tables;

    if (cw_salesman_is_reached($salesman)) {
        return (bool) cw_query_first_cell("select count(*) from $tables[salesman_premiums] where selected=1");
    }
    return false;
}

function cw_salesman_get_premiums($salesman, $lang, $where = '') {
    global $tables;

    return cw_query("select pp.*, IF(ppl.id != '', ppl.title, pp.title) as title from $tables[salesman_premiums] as pp left join $tables[salesman_premiums_lng] as ppl on pp.id=ppl.id and ppl.code='$lang' where salesman_customer_id='$salesman' $where order by orderby");
}

function cw_delete_salesman_premium($id) {
    global $tables;

    db_query("delete from $tables[salesman_premiums] where id='$id'");
    db_query("delete from $tables[salesman_premiums_lng] where id='$id'");
}

function cw_cleanup_target($salesman) {
    global $tables;

    $target = cw_salesman_get_target($salesman);
    if ($target['end_date'] <= time()) {
        db_query("delete from $tables[salesman_target] where salesman_customer_id='$salesman'");
        db_query("update $tables[salesman_premiums] set selected=0, active=0");
    }
}

#
# discounts
#
function cw_salesman_get_discounts($salesman_customer_id) {
    global $tables;

    return cw_query("select * from $tables[discount_coupons] where salesman_customer_id='$salesman_customer_id'");
}

function cw_salesman_get_customers($salesman_customer_id) {
    global $tables;

    return cw_query("select c.customer_id, c.usertype from $tables[customers_relations] as cr, $tables[customers] as c where cr.customer_id=c.customer_id and cr.salesman_customer_id='$salesman_customer_id'");
}

function cw_salesman_delete_discount($salesman_customer_id, $coupon) {
    global $tables;

    return db_query("delete from $tables[discount_coupons] where salesman_customer_id='$salesman_customer_id' and coupon='$coupon'");
}

function cw_salesman_get_discounts_all() {
    global $tables;

    return cw_query("select $tables[discount_coupons].* from $tables[discount_coupons], $tables[customers] as pc where $tables[discount_coupons].salesman_customer_id = pc.customer_id and pc.usertype='B'");
}

function cw_salesman_change_discount_status($id, $status) {
    global $tables, $smarty;

    db_query("update $tables[discount_coupons] set status='$status' where coupon='$id'");

    cw_load('mail', 'user');
    $coupon = cw_query_first("select * from $tables[discount_coupons] where coupon='$id'");
    if (!$coupon) return;

    $smarty->assign('coupon', $coupon);
    $userinfo = cw_user_get_info($coupon['customer_id']);
    $smarty->assign('userinfo', $userinfo);
    cw_call('cw_send_mail', array($config['Company']['orders_department'], $userinfo['email'], 'mail/salesman_coupon_subj.tpl', 'mail/salesman_coupon.tpl'));
}

function cw_is_salesman_coupon($coupon) {
    global $tables;

    $user_type = cw_query_first_cell("select usertype from $tables[customers] as c, $tables[discount_coupons] as dc where c.customer_id=dc.salesman_customer_id and dc.coupon='$coupon'");
    return ($user_type == 'B');
}

function cw_salesman_get_discount($products, $coupon, $membership_id, $warehouse) {
    global $tables;

    $data = cw_query_first("select * from $tables[discount_coupons] where coupon='$coupon'");
    $salesman = $data['warehouse'];

    $return = $salesman_commission = cw_salesman_get_commission($products, $salesman, $membership_id, 0, $warehouse);

    $return = $return * $data['discount']/100;

    $max_discount = cw_query_first_cell("select max_discount from $tables[customers] where customer_id='$salesman'");
    if ($max_discount) $return = $return * $max_discount/100;

    if ($return > $salesman_commission) $return = $salesman_commission;

    return $return;
}

function cw_salesman_get_commission($products, $salesman, $membership_id, $doc_id, $warehouse, $applied_coupon = '', $applied_discount = '', $part = 100) {
    global $tables;

    $salesman_commission_value = 0;

    $salesman_plan = cw_query_first_cell($sql="SELECT $tables[salesman_commissions].plan_id FROM $tables[salesman_commissions], $tables[salesman_plans], $tables[customers] WHERE $tables[salesman_commissions].plan_id=$tables[salesman_plans].plan_id AND $tables[salesman_commissions].salesman_customer_id='$salesman' AND $tables[customers].customer_id='$salesman' AND $tables[customers].status='Y' AND $tables[customers].status='Y' AND $tables[salesman_plans].status=1");

    if ($salesman_plan) {
        $tmp = cw_query("SELECT * FROM $tables[salesman_plans_commissions] WHERE plan_id='$salesman_plan' and membership_id='".$membership_id."'");
        $plan_info = array();
        if($tmp)
        foreach($tmp as $v)
            $plan_info[$v['item_type'].($v['item_id']>0?$v['item_id']:"")] = array("commission_type" => $v['commission_type'], "commission" => $v['commission']);
        unset($tmp);

        $products_hash = array();
        foreach ($products as $k => $product) {
            $percent_cost = $product['discounted_price']/100;
            unset($to_salesman);

            if ($plan_info["P".$product['product_id']])
                $to_salesman = $plan_info["P".$product['product_id']]['commission']*($plan_info["P".$product['product_id']]['commission_type'] == '$' ? $product['amount'] : $percent_cost);

                #
                # Check the categories commission rate
                #
                if (!isset($to_salesman)) {
                    $product_categories = cw_query_column("SELECT category_id FROM $tables[products_categories] WHERE product_id='$product[product_id]'");
                    foreach ($product_categories as $category_id) {
                        if (!isset($plan_info["C".$category_id]))
                            continue;

                        $tmp = $plan_info["C".$category_id]['commission']*($plan_info["C".$category_id]['commission_type'] == '$' ? $product['amount'] : $percent_cost);
                        if ($tmp > $to_salesman)
                            $to_salesman = $tmp;
                    }
                }
                #
                # Apply general value of the commission rate
                #
                if (!isset($to_salesman) && $plan_info['G'])
                    $to_salesman = $plan_info['G']['commission']*($plan_info['G']['commission_type'] == '$'?1:$percent_cost);

                $salesman_commission_value += price_format($to_salesman);
                $products_hash[$product['item_id']] = price_format($to_salesman);
        }
# kornev, the comission can be calculated partially, if the order is paid partially
        $salesman_commission_value = $salesman_commission_value*$part/100;

# kornev, calculate the next levels only if we placed an order
# kornev, the level calculation has been changed. The comission is devided by the levels.
        if ($salesman_commission_value && $doc_id) {
            # kornev, if discount is taken from the salesman account
            if (cw_is_salesman_coupon($applied_coupon)) {
                $from_account = cw_query_first_cell("select from_account from $tables[discount_coupons] where coupon='$applied_coupon'");
                if ($from_account) $salesman_commission_value -= $applied_discount;//cw_get_salesman_discount($products, $applied_coupon, $membership_id, $warehouse);
            }

            $salesman_level = cw_get_affiliate_level($salesman);
            $parents = array();
            $parents[] = array('customer_id' => $salesman, 'level' => $salesman_level);
            $__parents = cw_get_parents($salesman);
            if (is_array($__parents))
                $parents = array_merge($parents, $__parents);

            $div_commission_value = $salesman_commission_value;
                foreach ($parents as $v) {
                    $level = $v['level'];

                    if ($div_commission_value <= 0) continue;

                    $percent = cw_query_first_cell("SELECT commission FROM $tables[salesman_tier_commissions] WHERE level = '$level'");
                    $commission = price_format($div_commission_value*$percent/100);
                    $div_commission_value -= $commission;

                    if ($commission > 0) {
                        db_query ("INSERT INTO $tables[salesman_payment] (salesman_customer_id, doc_id, commissions, paid, affiliate, add_date) VALUES ('$v[customer_id]', '$doc_id', '$commission', 'N', '$salesman', '".(isset($xaff_force_time) ? $xaff_force_time : time())."')");
                        foreach ($products_hash as $id => $c) {
                            $c = price_format($c*$percent/100);
                            db_query("INSERT INTO $tables[salesman_product_commissions] (item_id, doc_id, product_commission, salesman_customer_id) VALUES ('$id', '$doc_id', '$c','$v[customer_id]')");
                            $products_hash[$id] -= $c;
                        }
                    }
                }
        }
    }
    return $salesman_commission_value;
}

function cw_salesman_get_customer($customer_id) {
    global $tables;

    return cw_query_first("select p.* from $tables[customers] as p, $tables[customers_salesman_info] as csi where p.customer_id=csi.parent_customer_id and csi.customer_id='$customer_id'");
}

#
# Get salesman affliates
#
function cw_get_affiliates($user, $level = -1) {
	global $tables, $config;

	if(!$user)
		return false;

	if($level == -1)
		$level = cw_get_affiliate_level($user);
	$childs = cw_query("select customer_id from $tables[customers_salesman_info] where parent_customer_id = '$user'");
	if($childs) {
		for($x = 0; $x < count($childs); $x++) {
			$childs[$x]['level'] = cw_get_affiliate_level($childs[$x]['customer_id']);
			$childs[$x]['sales'] = cw_query_first_cell("SELECT SUM(commissions) FROM $tables[salesman_payment] WHERE customer_id='".$childs[$x]['customer_id']."'");
			$tmp = cw_get_affiliates($childs[$x]['customer_id'], $level+1);
			$childs_sales = 0;
			if($tmp) {
				$childs[$x]['childs'] = $tmp;
				for($y = 0; $y < count($tmp); $y++)
					$childs_sales += $tmp[$y]['sales']+$tmp[$y]['childs_sales'];
			}
			$childs[$x]['childs_sales'] = $childs_sales;
		}
	}
	return $childs;
}

function cw_get_affiliates_flat($user, $keys_only = false) {
    global $tables, $config;

    if(!$user)
        return false;

    $ret = array();
    $childs = cw_query("SELECT * FROM $tables[customers] WHERE parent = '$user'");
    if($childs) {
        foreach($childs as $val) {
            $ret[$val['customer_id']] = $keys_only?$val['customer_id']:$val;
            $tmp = cw_get_affiliates($val['customer_id']);
            if($tmp)
                foreach($tmp as $val)
                    $ret[$val['customer_id']] = $keys_only?$val['customer_id']:$val;
        }
    }
    return $ret;
}

#
# Get afiiliate level
#
function cw_get_affiliate_level($user) {
	global $tables;

	if(!$user)
		return false;

	$level = 0;
	do {
		$user = cw_query_first_cell("select parent_customer_id from $tables[customers_salesman_info] where customer_id='$user'");
		$user = addslashes($user);
		$level++;
	} while($user);
	return $level;
}

#
# Get parents array
#
function cw_get_parents($user) {
	global $tables, $config;
	$parent = cw_query_first_cell("SELECT salesman_customer_id FROM $tables[customers_relations] WHERE customer_id='$user'");
	if($parent) {
		$parents[] = array("customer_id" => $parent, "level" => cw_get_affiliate_level($parent));
		$parents = cw_array_merge($parents, cw_get_parents($parent));
	}
	return $parents;
}

# Clear statistics
function cw_clear_stats_xaff($rsd_limit) {
	global $tables;

	if (empty($rsd_limit)) {
		db_query("DELETE FROM $tables[salesman_adv_clicks]");
		db_query("DELETE FROM $tables[salesman_clicks]");
		db_query("DELETE FROM $tables[salesman_views]");

	} else {
		db_query("DELETE FROM $tables[salesman_adv_clicks] WHERE add_date < '$rsd_limit'");
		db_query("DELETE FROM $tables[salesman_clicks] WHERE add_date < '$rsd_limit'");
		db_query("DELETE FROM $tables[salesman_views] WHERE add_date < '$rsd_limit'");
	}

	return cw_get_langvar_by_name("msg_adm_summary_aff_stat_del");
}

function cw_salesman_get_list_smarty() {
    $users = cw_user_get_short_list('B');
    return $users;
}

?>
