<?php
// CartWorks.com - Promotion Suite 

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }
/*
if ($special_offers_apply['free_shipping'] && is_array($shipping)) {
	foreach ($shipping as $k=>$v) $shipping[$k]['rate'] = 0;
}
*/

global $special_offers_apply;
if (!empty($special_offers_apply['free_shipping']['method']) && is_array($special_offers_apply['free_shipping']['method']) && !in_array($shipping_id,$special_offers_apply['free_shipping']['method'])) return false;

if ($special_offers_apply['free_shipping']['type']=='Y') {
	$total_ship_items = 0;
	$shipping_freight = 0;
} 
elseif (in_array($special_offers_apply['free_shipping']['type'],array('C','S')) && !empty($special_offers_apply['free_shipping']['products'])) {
    $total_weight['original'] = $total_weight['apply']; //$total_weight['valid'];
	foreach ($special_offers_apply['free_shipping']['products'] as $pid=>$qty) {
		foreach ($products as $kk=>$product) {
			if ($product['productid'] == $pid) {
				
				$tmp_qty = min($qty,$product['amount']);
				
				if ($tmp_qty == 0) continue;
				
				if ($addons["Egoods"] && $product["distribution"] != "")
					continue;
			
                // Calculate total_cost and total_weight for selection condition

                if (
                    $product['free_shipping'] != 'Y'
                    || $config['Shipping']['free_shipping_weight_select'] == 'Y'
                ) {

                    $total_cost['valid']['DST'] -= $product["subtotal"]*$tmp_qty/$product['amount'];
                    $total_cost['valid']['ST']  -= price_format($product['price'] * $tmp_qty);
                    $total_weight['valid']      -= $product["weight"] * $tmp_qty;
                }

				# Calculate total_cost and total_+weight for shipping calculation
				if ($product["free_shipping"] == "Y")
					continue;

                if (
                    $product['shipping_freight'] <= 0
                    || $config['Shipping']['replace_shipping_with_freight'] != 'Y'
                ) {

                    $total_cost['apply']['DST'] -= $product["subtotal"]*$tmp_qty/$product['amount'];
                    $total_cost['apply']['ST']  -= price_format($product['price'] * $tmp_qty);
                    $total_weight['apply']      -= $product["weight"] * $tmp_qty;

                    if ($product['product_type'] != 'C') {
                        $total_ship_items -= $tmp_qty;
                    }

                }

				$shipping_freight -= $product["shipping_freight"] * $tmp_qty;
				$qty -= $tmp_qty;
			}
		}
	}
}

// CartWorks.com - Promotion Suite 
?>
