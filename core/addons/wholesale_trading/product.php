<?php
cw_load('taxes');

$price_where = "0, '$user_account[memebrship_id]'";

$wresult = cw_query ("SELECT $tables[products_prices].quantity, $tables[products_prices].price FROM $tables[products_prices] where $tables[products_prices].product_id='$product_id' AND $tables[products_prices].membership_id IN ($price_where) AND $tables[products_prices].quantity > 1 AND $tables[products_prices].variant_id = 0 group by $tables[products_prices].quantity order by $tables[products_prices].quantity");

if ($wresult) {
	$last_price = doubleval(cw_query_first_cell("SELECT MIN(price) FROM $tables[products_prices] WHERE quantity = 1 AND membership_id IN ($price_where) AND variant_id = 0 AND product_id = '$product_id'"));
	$last_k = false;
	foreach ($wresult as $wk => $wv) {
		if ($wv['price'] > $last_price) {
			unset($wresult[$wk]);
			continue;
		}

		$last_price = $wv['price'];
		$_taxes = cw_tax_price($wv['price'], $user_info, $product_id);
		$wresult[$wk]['taxed_price'] = $_taxes['taxed_price'];
		$wresult[$wk]['taxes'] = $_taxes['taxes'];

		if ($last_k !== false && isset($wresult[$last_k])) {
			$wresult[$last_k]['next_quantity'] = $wv['quantity']-1;
			if ($product_info['min_amount'] > $wresult[$last_k]['next_quantity']) {
				unset($wresult[$last_k]);

			} elseif ($product_info['min_amount'] > $wresult[$last_k]['quantity']) {
				$wresult[$last_k]['quantity'] = $product_info['min_amount'];
			}
		}
		$last_k = $wk;

	}
	$wresult = array_values($wresult);

	if (count($wresult) > 0) {
		$wresult[count($wresult)-1]['next_quantity'] = 0;
		$smarty->assign ("product_wholesale", $wresult);
	}
}

?>
