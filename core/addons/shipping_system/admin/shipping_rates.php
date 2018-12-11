<?php
if ($type != 'R') $type = 'D';

$type_condition = " AND type='$type'";

if (AREA_TYPE == 'A' && $action == 'copy_warehouses' && $copy['source_warehouse'] && $copy['source_warehouse'] != $copy['dest_warehouse']) {
    $zones = cw_query_column("select zone_id from $tables[zones] where warehouse_customer_id='$copy[dest_warehouse]' and is_shipping=1");
    if ($zones)
    foreach($zones as $zone_id) cw_call('cw_shipping_delete_zone', array($zone_id));
    db_query("delete from $tables[shipping_rates] where warehouse_customer_id='$copy[dest_warehouse]'");

    $source_rates= cw_query("select * from $tables[shipping_rates] where warehouse_customer_id='$copy[source_warehouse]'");
    if (is_array($source_rates))
    foreach($source_rates as $rate) {
        $zone_id = $rate['zone_id'];
        if (!$zones_convertation[$zone_id]) {
            $zone = cw_query_first("select * from $tables[zones] where zone_id='$zone_id'");
            unset($zone['zone_id']);
            $zone['warehouse_customer_id'] = $copy['dest_warehouse'];
            $zones_convertation[$zone_id] = cw_array2insert('zones', $zone);

            $elements = cw_query("select * from $tables[zone_element] where zone_id='$zone_id'");
            if ($elements)
            foreach($elements as $el) {
                $el['zone_id'] = $zones_convertation[$zone_id];
                cw_array2insert('zone_element', $el, 1);
            }
        }
        $rate['warehouse_customer_id'] = $copy['dest_warehouse'];
        $rate['zone_id'] = $zones_convertation[$zone_id];
        unset($rate['rate_id']);
        cw_array2insert('shipping_rates', $rate);
    }

    cw_header_location("index.php?target=$target&division_id=".$copy['dest_warehouse']);
}


if ($REQUEST_METHOD=="POST") {

	if ($action == 'delete' && is_array($posted_data)) {
        $deleted = false;
        foreach ($posted_data as $rate_id=>$v) {
            if (empty($v['to_delete'])) continue;

            db_query("DELETE FROM $tables[shipping_rates] WHERE rate_id='$rate_id' $warehouse_condition $type_condition");
            $deleted = true;
        }

        if ($deleted)
            $top_message['content'] = cw_get_langvar_by_name("msg_shipping_rates_del");
	}

	if ($action == "update") {
		#
		# Update shipping table
		#
		if (is_array($posted_data)) {
			foreach ($posted_data as $rate_id=>$v) {
				cw_array2update("shipping_rates",
					array(
						"minweight" => cw_convert_number($v['minweight']),
						"maxweight" => cw_convert_number($v['maxweight']),
						"mintotal" => cw_convert_number($v['mintotal']),
						"maxtotal" => cw_convert_number($v['maxtotal']),
						"rate" => cw_convert_number($v['rate']),
						"item_rate" => cw_convert_number($v['item_rate']),
						"rate_p" => cw_convert_number($v['rate_p']),
                        "overweight" => cw_convert_number($v['overweight']),
                        "overweight_rate" => cw_convert_number($v['overweight_rate']),
                        "rate_p" => cw_convert_number($v['rate_p']),
						"weight_rate" => cw_convert_number($v['weight_rate']),
                        'apply_to' => ($v['apply_to']=="DST"?"DST":"ST"),
					),
					"rate_id='$rate_id' $warehouse_condition $type_condition"
				);
			}

			$top_message['content'] = cw_get_langvar_by_name("msg_shipping_rates_upd");
		}
	}

	if ($action == "add" && $shipping_id_new) {
			cw_array2insert("shipping_rates",
				array(
					"shipping_id" => $shipping_id_new,
					"minweight" => cw_convert_number($minweight_new),
					"maxweight" => cw_convert_number($maxweight_new),
					"maxamount" => cw_convert_number($maxamount_new),
					"mintotal" => cw_convert_number($mintotal_new),
					"maxtotal" => cw_convert_number($maxtotal_new),
					"rate" => cw_convert_number($rate_new),
					"item_rate" => cw_convert_number($item_rate_new),
					"rate_p" => cw_convert_number($rate_p_new),
					"weight_rate" => cw_convert_number($weight_rate_new),
					"warehouse_customer_id" => $current_area=='A'?$division_id:$user_account['warehouse_customer_id'],
					"zone_id" => $zone_id_new,
					"type" => $type,
                    "overweight" => cw_convert_number($overweight_new),
                    "overweight_rate" => cw_convert_number($overweight_rate_new),
                    'apply_to' => ($apply_to_new=="DST"?"DST":"ST"),
				)
			);
			$top_message['content'] = cw_get_langvar_by_name("msg_shipping_rate_add");
	}

	cw_header_location("index.php?target=$target&zone_id=$zone_id&shipping_id=$shipping_id&type=$type");
}

$zone_condition = $zone_id?"and $tables[shipping_rates].zone_id='$zone_id'":'';
$method_condition = $shipping_id?"and $tables[shipping_rates].shipping_id='$shipping_id'":'';

if ($division_id and $current_area=='A')
    $warehouse_condition = " and warehouse_customer_id='$division_id'";

$shipping_rates = cw_query("SELECT $tables[shipping_rates].*, $tables[shipping].shipping, $tables[shipping].shipping_time FROM $tables[shipping], $tables[shipping_rates] WHERE $tables[shipping_rates].shipping_id=$tables[shipping].shipping_id AND $tables[shipping].active=1 $warehouse_condition $type_condition $zone_condition $method_condition ".($type=="R"?" AND code!='' ":'')." ORDER BY $tables[shipping].orderby, $tables[shipping_rates].maxweight");

#
# Prepare zones list
#
$zones = array(array("zone_id"=>0,"zone"=>cw_get_langvar_by_name("lbl_zone_default")));
$_tmp = cw_query("SELECT zone_id, zone_name as zone FROM $tables[zones] WHERE 1 $warehouse_condition and is_shipping=1 ORDER BY zone_id");
if (!empty($_tmp))
	$zones = cw_array_merge($zones,$_tmp);

if (is_array($zones) && is_array($shipping_rates)) {
	foreach ($zones as $zone) {
		$shipping_rates_list = array();
		foreach ($shipping_rates as $shipping_rate) {
			if ($shipping_rate['zone_id'] != $zone['zone_id'])
				continue;

			$shipping_rates_list[$shipping_rate['shipping_id']]['shipping'] = $shipping_rate['shipping'];
			$shipping_rates_list[$shipping_rate['shipping_id']]['rates'][] = $shipping_rate;
		}

		$_zones_list = array();
		$_zones_list['zone'] = $zone;
		$_zones_list['shipping_methods'] = $shipping_rates_list;
		$zones_list[] = $_zones_list;
	}
}

$carriers = cw_shipping_get_carriers(true);
$carriers_ids = array(-1);

if (!empty($carriers)) {
	$carriers_ids = array();

	foreach ($carriers as $k => $v) {
		$carriers_ids[] = $v[carrier_id];
	}
}

if ($type == "R") {
	$markup_condition .= " and code!=''";
	$shippings = cw_query("select * from $tables[shipping] where active=1 $markup_condition AND carrier_id IN (" . implode(",", $carriers_ids). ") ORDER BY orderby");
}
else
	$shippings = cw_query("select * from $tables[shipping] where active=1 AND carrier_id IN (" . implode(",", $carriers_ids). ") ORDER BY orderby");

$smarty->assign('shippings', $shippings);

$smarty->assign('zones', $zones);
$smarty->assign('shipping_rates', $shipping_rates);
$smarty->assign('shipping_rates_avail', (is_array($shipping_rates) ? count($shipping_rates) : 0));
$smarty->assign('zones_list', $zones_list);
$smarty->assign('type', $type);
$smarty->assign('zone_id', $zone_id);
$smarty->assign('shipping_id', $shipping_id);

$location[] = array(cw_get_langvar_by_name('lbl_shipping_rates'), '');
$smarty->assign('main', 'rates');

$smarty->assign('current_main_dir', 'addons/shipping_system');
$smarty->assign('current_section_dir', 'admin');
