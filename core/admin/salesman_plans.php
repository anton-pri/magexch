<?php
# [TOFIX]
# kornev, move it to the addon dir
if (!$addons['Salesman'])
    cw_header_location('index.php');

cw_load('category', 'product');

if ($action == 'default_plan') {
    if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[config] WHERE name='default_affiliate_plan' AND category=''") == 0)
	    db_query("INSERT INTO $tables[config] (name, value, category, defvalue) VALUES ('default_affiliate_plan', '$plan_id', '', '')");
    else
	    db_query("UPDATE $tables[config] SET value='$plan_id' WHERE name='default_affiliate_plan' AND category=''");
    cw_header_location("index.php?target=salesman_plans");
}
	
if ($action == "update" && is_array($plans)) {
    foreach($plans as $k=>$v) {
        if ($v['del']) {
            db_query("delete from $tables[salesman_plans] WHERE plan_id='$k'");
            db_query("update $tables[salesman_commissions] SET plan_id='0' WHERE plan_id='$k'");
            if ($config['default_affiliate_plan'] == $k)
                db_query("update $tables[config] set value='0' where name='default_affiliate_plan'");
            continue;
        }
        if ($k == 0 && !empty($v['title'])) $k = cw_array2insert('salesman_plans', array('title' => $v['title']));
        if (!$k) continue;
        cw_array2update('salesman_plans', $v, "plan_id = '$k'", array('title', 'status'));
    }

    cw_header_location("index.php?target=salesman_plans");
}

if($action == 'edit' && is_array($plans)) {
    foreach ($plans as $pid=>$plan)
        db_query ("UPDATE $tables[salesman_plans] SET title='$plan[plan_title]', status='$plan[status]'  WHERE plan_id='$pid'");
    cw_header_location("index.php?target=salesman_plans");		
}

if ($action == "delete_rate" && is_array($product_id)) {
    foreach($product_id as $id)
        db_query("DELETE FROM $tables[salesman_plans_commissions] WHERE id='$id'");
    cw_header_location("index.php?target=salesman_plans&mode=edit&plan_id=$plan_id");
}

if ($action == "modify" || $action == "create") {

    if (is_array($products))
    foreach($products as $id=>$v) {
        if ($id == '0') {
            if ($v['category_id']) {
                $is_exists = cw_query_first_cell("SELECT COUNT(*) FROM $tables[salesman_plans_commissions] WHERE plan_id = '$plan_id' AND item_id = '$v[category_id]' AND item_type = 'C' and membership_id='$v[membership_id]'") > 0;
                if (!$is_exists)
                    db_query($sql="INSERT INTO $tables[salesman_plans_commissions] (plan_id, commission, commission_type, item_id, item_type, membership_id) VALUES ('$plan_id', '".addslashes(cw_convert_number($v['commission']))."', '$v[commission_type]', '$v[category_id]', 'C', '$v[membership_id]')");
            }
            elseif ($v['product_id']) {
                $products = explode(' ', $v['product_id']);
                if (is_array($products))
                foreach($products as $product_id) {
                    if (!$product_id) continue;
                    $is_exists = cw_query_first_cell("SELECT COUNT(*) FROM $tables[salesman_plans_commissions] WHERE plan_id = '$plan_id' AND item_id = '$product_id' AND item_type = 'P' and membership_id='$v[membership_id]'") > 0;
                    if (!$is_exists)
                        db_query("INSERT INTO $tables[salesman_plans_commissions] (plan_id, commission, commission_type, item_id, item_type, membership_id) VALUES ('$plan_id', '".addslashes(cw_convert_number($v['commission']))."', '$v[commission_type]', '$product_id', 'P', '$v[membership_id]')");
                }
            }
        }
        else 
            db_query("update $tables[salesman_plans_commissions] SET commission='".addslashes(cw_convert_number($v['commission']))."', commission_type='$v[commission_type]', membership_id='$v[membership_id]' WHERE id='$id'");
    }

    if (is_array($basic))
    foreach($basic as $membership_id=>$val) {
        if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[salesman_plans_commissions] WHERE plan_id='$plan_id' AND item_id='0' AND item_type='G' and membership_id='$membership_id'") == "0")
    	    db_query("INSERT INTO $tables[salesman_plans_commissions] (plan_id, commission, commission_type, item_type, membership_id) VALUES('$plan_id', '$val[commission]', '$val[commission_type]', 'G', '$membership_id')");
        else
		    db_query("UPDATE $tables[salesman_plans_commissions] SET commission='$val[commission]', commission_type='$val[commission_type]' WHERE plan_id='$plan_id' AND item_id='0' AND item_type='G' and membership_id='$membership_id'");
    }
	db_query("UPDATE $tables[salesman_plans] SET min_paid = '$min_paid' WHERE plan_id='$plan_id'");

    cw_header_location("index.php?target=salesman_plans&mode=edit&plan_id=$plan_id");
}

if ($mode == 'edit') {

	if ($plan_id) {
		$salesman_plan_info = cw_query_first ("SELECT * FROM $tables[salesman_plans] WHERE plan_id='$plan_id'");
		$salesman_plans_commissions = cw_query("SELECT * FROM $tables[salesman_plans_commissions] WHERE plan_id='$plan_id'");

		if (is_array($salesman_plans_commissions)) {
			foreach($salesman_plans_commissions as $k=>$v) {
				if ($v['item_type'] == "P")
					$salesman_plans_commissions[$k]['product'] = cw_func_call('cw_product_get', array('id' => $v['item_id'], 'user_account' => $user_account));
				if ($v['item_type'] == "C")
					$salesman_plans_commissions[$k]['category'] = cw_func_call('cw_category_get', array('cat' => $v['item_id']));
				if ($v['item_type'] == "G") {
					$general_commission[$v['membership_id']] = $v;
                    unset($salesman_plans_commissions[$k]);
                }
			}
		}
		
		$smarty->assign('salesman_plans_commissions', $salesman_plans_commissions);
		$smarty->assign('general_commission', $general_commission);
		$smarty->assign('salesman_plan_info', $salesman_plan_info);
		$smarty->assign('mode', 'modify');
	}
	else
		cw_header_location("index.php?target=salesman_plans");

    $smarty->assign('plan_id', $plan_id);

	$location[] = array(cw_get_langvar_by_name('lbl_modify_plan'), '');
    $location[] = array($salesman_plan_info['title'], '');

	$smarty->assign('main', 'plans_edit');
}
else {
    $salesman_plans = cw_query("select * from $tables[salesman_plans] order by title");
    $smarty->assign ("salesman_plans", $salesman_plans);
	$smarty->assign('main', 'plans');
}

$smarty->assign('memberships', cw_user_get_memberships(array('C', 'R')));
?>
