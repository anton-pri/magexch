<?php
if ($addons['shipping_system'] != 'Y')
	cw_header_location("index.php?target=error_message&error=shipping_disabled");

#
# This value is used as a default top range value
# for weight and order subtotal ranges (used in Smarty template)
#
$maxvalue = 999999.99;


#
# Shipping rates - D (defined rates)
# Shipping markups - R (for realtime methods only)
#
if ($type != "R") $type = "D";

$type_condition = " AND type='$type'";

$warehouse_condition = "and warehouse_customer_id='".$user_account['warehouse_customer_id']."'";

if ($REQUEST_METHOD=="POST") {

	if ($action == "delete") {
		#
		# Delete shipping option
		#
		if (is_array($posted_data)) {
			$deleted = false;
			foreach ($posted_data as $rate_id=>$v) {
				if (empty($v['to_delete']))
					continue;

				db_query("DELETE FROM $tables[shipping_rates] WHERE rate_id='$rate_id' $warehouse_condition $type_condition");
				$deleted = true;
			}

			if ($deleted)
				$top_message['content'] = cw_get_langvar_by_name("msg_shipping_rates_del");
		}
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
						"weight_rate" => cw_convert_number($v['weight_rate'])
					),
					"rate_id='$rate_id' $warehouse_condition $type_condition"
				);
			}

			$top_message['content'] = cw_get_langvar_by_name("msg_shipping_rates_upd");
		}
	}

	if ($action == "add") {
		#
		# Add new shipping rate
		#
		if ($shipping_id_new) {
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
					"warehouse_customer_id" => $user_account['warehouse_customer_id'],
					"zone_id" => $zone_id_new,
					"type" => $type,
                    "overweight" => cw_convert_number($overweight_new),
                    "overweight_rate" => cw_convert_number($overweight_rate_new),
				)
			);
			$top_message['content'] = cw_get_langvar_by_name("msg_shipping_rate_add");
		}
	}

	cw_header_location("index.php?target=shipping_rates&zone_id=$zone_id&shipping_id=$shipping_id&type=$type");
}

$zone_condition = ($zone_id!=""?"and $tables[shipping_rates].zone_id='$zone_id'":"");
$method_condition = ($shipping_id!=""?"and $tables[shipping_rates].shipping_id='$shipping_id'":"");

$shipping_rates = cw_query("SELECT $tables[shipping_rates].*, $tables[shipping].shipping, $tables[shipping].shipping_time, $tables[shipping].destination FROM $tables[shipping], $tables[shipping_rates] WHERE $tables[shipping_rates].shipping_id=$tables[shipping].shipping_id AND $tables[shipping].active=1 $warehouse_condition $type_condition $zone_condition $method_condition ".($type=="R"?" AND code!='' ":'')." ORDER BY $tables[shipping].orderby, $tables[shipping_rates].maxweight");

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
			$shipping_rates_list[$shipping_rate['shipping_id']]['destination'] = $shipping_rate['destination'];
			$shipping_rates_list[$shipping_rate['shipping_id']]['rates'][] = $shipping_rate;

		}

		$_zones_list = array();
		$_zones_list['zone'] = $zone;
		$_zones_list['shipping_methods'] = $shipping_rates_list;
		$zones_list[] = $_zones_list;
	}
}

if ($type == "R") {
	$markup_condition .= " AND code!=''";
	$shipping = cw_query("SELECT * FROM $tables[shipping] WHERE active=1 $markup_condition ORDER BY orderby");
}
else
	$shipping = cw_query("SELECT * FROM $tables[shipping] WHERE active=1 ORDER BY orderby");

$smarty->assign('shipping', $shipping);

$smarty->assign('zones', $zones);
$smarty->assign('shipping_rates', $shipping_rates);
$smarty->assign('shipping_rates_avail', (is_array($shipping_rates) ? count($shipping_rates) : 0));
$smarty->assign('zones_list', $zones_list);
$smarty->assign('type', $type);
$smarty->assign('zone_id', $zone_id);
$smarty->assign('shipping_id', $shipping_id);
$smarty->assign('maxvalue', $maxvalue);

$smarty->assign('main', "shipping_rates");
