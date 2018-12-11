<?php


$tax_id = intval(@$tax_id);

if ($REQUEST_METHOD == "POST") {
	$redirect_to = "";

	if ($action == "tax_options" && $current_area == 'A') {
		if (is_array($posted_data)) {
			foreach ($posted_data as $k=>$v) {
				if (!in_array($v, array("Y","N")) && !in_array($k, array("tax_payment_amount_type", "tax_payment_amount")))
					$v = "N";

				db_query($sql="UPDATE $tables[config] SET value='$v' WHERE name='$k' AND category='Taxes'");
			}

			$top_message['content'] = cw_get_langvar_by_name("msg_taxes_options_updated");
		}
	}
	elseif ($action == "delete" && $current_area == 'A') {
		#
		# Delete selected taxes
		#
		if (!empty($to_delete) && is_array($to_delete)) {
			foreach ($to_delete as $tax_id=>$v) {
				db_query("DELETE FROM $tables[taxes] WHERE tax_id='$tax_id'");
				$rate_ids = cw_query_column("SELECT rate_id FROM $tables[tax_rates] WHERE tax_id='$tax_id'");
				db_query("DELETE FROM $tables[tax_rates] WHERE tax_id='$tax_id'");
				if (!empty($rate_id))
					db_query("DELETE FROM $tables[tax_rate_memberships] WHERE rate_id IN ('".implode("','", $rate_ids)."')");
			}

			$top_message['content'] = cw_get_langvar_by_name("msg_taxes_deleted");
		}
	}
	elseif ($action == "update" &&  $current_area == 'A') {
		#
		# Update taxes list
		#
		if (!empty($posted_data) && is_array($posted_data)) {
			foreach ($posted_data as $tax_id=>$v) {
				db_query("UPDATE $tables[taxes] SET active='$v[active]', priority='$v[tax_priority]' WHERE tax_id='$tax_id'");
			}
			$top_message['content'] = cw_get_langvar_by_name("msg_taxes_updated");
		}
	}
	elseif ($action == 'details' && $current_area == 'A') {

		$posted_data['formula'] = preg_replace('/^=/', '', $posted_data['formula']);

		if (empty($posted_data['tax_name']) || empty($posted_data['formula'])) {
            $tmp_tax_details = &cw_session_register('tmp_tax_details');
            $tmp_tax_details = $posted_data;
            $tax_details['tax_id'] = $tax_id;

			$top_message = array('content' => cw_get_langvar_by_name('err_filling_form'), 'type' => 'E');

			$redirect_to = empty($tax_id) ? '&mode=add' : "&tax_id=$tax_id";
		}
		else {
			if (empty($tax_id)) $tax_id = cw_array2insert('taxes', array('tax_name' => $posted_data['tax_name']));

			cw_array2update('taxes', $posted_data, "tax_id='$tax_id'", array('tax_name', 'formula', 'address_type', 'active', 'price_includes_tax', 'display_including_tax', 'display_info', 'regnumber', 'priority', 'use_info'));
            cw_array2insert('languages_alt', array('code'=> $current_language, 'name'=>'tax_'.$tax_id, 'value'=>$posted_data['tax_display_name']), true);
            
			$redirect_to = "&tax_id=$tax_id";
            $top_message = array('content' => cw_get_langvar_by_name('msg_tax_upd'));
		}
	}
	elseif ($action == "delete_rates" && !empty($tax_id)) {
		#
		# Delete selected tax rates
		#
		if (!empty($to_delete) && is_array($to_delete)) {
			db_query("DELETE FROM $tables[tax_rates] WHERE rate_id IN ('".implode("','", array_keys($to_delete))."')");
			$top_message['content'] = cw_get_langvar_by_name("msg_tax_rate_del");
			$top_message['anchor'] = "rates";
		}

		$redirect_to = "&tax_id=$tax_id";
	}
	elseif ($action == "update_rates" && !empty($tax_id)) {
		#
		# Update tax rates
		#
		if (!empty($posted_data) && is_array($posted_data)) {
			foreach ($posted_data as $rate_id=>$v) {
				$rate_value = cw_convert_number($v['rate_value'], "3".substr($config['Appearance']['number_format'], 1));
				$rate_type = $v['rate_type'];
				if (!in_array($rate_type, array("%","$")))
					$rate_type = "%";

				db_query("UPDATE $tables[tax_rates] SET rate_value='$rate_value', rate_type='$rate_type' WHERE rate_id='$rate_id' ");
			}

			$top_message['content'] = cw_get_langvar_by_name("msg_tax_rate_upd");
			$top_message['anchor'] = "rates";
		}

		$redirect_to = "&tax_id=$tax_id";
	}
	elseif ($action == 'rate_details' && !empty($tax_id)) {
		$rate_id = intval(@$rate_id);
		$rate_value = cw_convert_number($rate_value, "3".substr($config['Appearance']['number_format'], 1));
		$zone_id = intval($zone_id);
		if (!in_array($rate_type, array("%","$")))
			$rate_type = "%";

		if (empty($membership_ids) || in_array(-1, $membership_ids))
			$membership_ids_where = "IS NULL ";
		else
			$membership_ids_where = "IN ('".implode("','", $membership_ids)."') ";

		if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[tax_rates] LEFT JOIN $tables[tax_rate_memberships] ON $tables[tax_rates].rate_id = $tables[tax_rate_memberships].rate_id WHERE $tables[tax_rates].tax_id = '$tax_id' AND $tables[tax_rates].rate_id != '$rate_id' AND $tables[tax_rates].zone_id = '$zone_id' AND $tables[tax_rate_memberships].membership_id ".$membership_ids_where) == 0) {
			$rate_formula = preg_replace("/^=/", "", $rate_formula);

			$query_data = array(
				"zone_id" => $zone_id,
				"formula" => $rate_formula,
				"rate_value" => $rate_value,
				"rate_type" => $rate_type
			);

			if (!empty($rate_id)) {
				cw_array2update("tax_rates", $query_data, "rate_id='$rate_id' ");
				db_query("DELETE FROM $tables[tax_rate_memberships] WHERE rate_id='$rate_id'");
				$top_message['content'] = cw_get_langvar_by_name("msg_tax_rate_upd");
			}
			else {
				$query_data['tax_id'] = $tax_id;
				$rate_id = cw_array2insert("tax_rates", $query_data);
				$top_message['content'] = cw_get_langvar_by_name("msg_tax_rate_add");
			}
			cw_membership_update("tax_rate", $rate_id, $membership_ids, "rate_id");
		}
		else {
			$top_message['content'] = cw_get_langvar_by_name("msg_err_tax_rate_add");
			$top_message['type'] = "E";
		}

		$top_message['anchor'] = "rates";

		$redirect_to = "&tax_id=$tax_id";
	}

	cw_header_location('index.php?target='.$target.$redirect_to);
}

if ($mode == "add" || !empty($tax_id)) {

    $location[] = array(cw_get_langvar_by_name('lbl_taxes'), 'index.php?target='.$target);    
	$location[] = array(cw_get_langvar_by_name('lbl_tax_details'), '');

	if (!empty($tax_id)) {
		$tax_details = cw_query_first("SELECT * FROM $tables[taxes] WHERE tax_id='$tax_id'");
        $tax_details['tax_display_name'] =  cw_get_languages_alt('tax_'.$tax_id);
    }

	if (empty($tax_details)) {
		$mode = "add";
		if (cw_session_is_registered("tmp_tax_details")) {
			$tmp_tax_details = &cw_session_register("tmp_tax_details");
			$tax_details = $tmp_tax_details;
			cw_session_unregister("tmp_tax_details");
		}
	}
	else {
		$tax_rates = cw_query("SELECT $tables[tax_rates].*, $tables[zones].zone_name FROM $tables[tax_rates] LEFT JOIN $tables[zones] ON $tables[tax_rates].zone_id=$tables[zones].zone_id WHERE $tables[tax_rates].tax_id='$tax_id' ORDER BY $tables[zones].zone_name, $tables[tax_rates].rate_value");
		$tmp = cw_user_get_memberships(array('C', 'R'));
		if (!empty($tax_rates))
		foreach ($tax_rates as $k => $v) {
			$keys = cw_query_column("SELECT membership_id FROM $tables[tax_rate_memberships] WHERE rate_id = '$v[rate_id]'");
			if (!empty($tmp) && !empty($keys)) {
			    $tax_rates[$k]['membership_ids'] = array();
				foreach ($tmp as $m) {
				    if (in_array($m['membership_id'], $keys))
				        $tax_rates[$k]['membership_ids'][$m['membership_id']] = $m['membership'];
				}
		    }
		}

		$smarty->assign('tax_rates', $tax_rates);

        $rate_details = array();

		if (!empty($rate_id) && !empty($tax_rates) && is_array($tax_rates)) {
			$rate_formula = "";
			foreach ($tax_rates as $k=>$v) {
				if ($v['rate_id'] == $rate_id) {
					$rate_details = $v;
					break;
				}
			}
		}

        $smarty->assign('rate_details', $rate_details);

		$zones = cw_query("SELECT * FROM $tables[zones] WHERE is_shipping=0 and warehouse_customer_id = '".cw_get_default_account_for_zones()."' ORDER BY zone_name");
		$smarty->assign('zones', $zones);
	}

	if (is_array($taxes_units)) {
		#
		# Correct the tax formula units description
		#
		foreach ($taxes_units as $k=>$v) {
			$taxes_units[$k] = cw_get_langvar_by_name($v);
		}

		$_taxes = cw_query("SELECT tax_id, tax_name FROM $tables[taxes] WHERE tax_id!='$tax_id' ORDER BY tax_name");

		$smarty->assign('taxes_units', $taxes_units);
	}

	$smarty->assign('tax_details', $tax_details);

	$smarty->assign('main', "tax_edit");
}
else {
    $location[] = array(cw_get_langvar_by_name('lbl_taxes'), '');

	$taxes = cw_query("SELECT $tables[taxes].*, COUNT($tables[tax_rates].tax_id) as rates_count FROM $tables[taxes] LEFT JOIN $tables[tax_rates] ON $tables[tax_rates].tax_id=$tables[taxes].tax_id GROUP BY $tables[taxes].tax_id ORDER BY priority, tax_name");

	$smarty->assign('taxes', $taxes);
	$smarty->assign('main', "taxes");

}
$smarty->assign('mode', $mode);

cw_load('user');
$smarty->assign('memberships', cw_user_get_memberships(array('C', 'R')));
?>
