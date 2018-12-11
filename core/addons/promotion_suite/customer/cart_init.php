<?php
if (defined('IS_AJAX') && constant('IS_AJAX') && !defined('AOM')) return true;

if (!empty($action) && $action == 'clear_cart') {
    cw_session_unregister('special_offers_apply');
    return true;
}

// PS
cw_load('warehouse','cart','cart_process','checkout');

$special_offers_apply =& cw_session_register("special_offers_apply");

$cart_hash = crc32(serialize(array($cart,$user_account,$user_address)));
if (isset($special_offers_apply['cart_hash']) && $special_offers_apply['cart_hash'] == $cart_hash) return true;

// Cart page requested but offer is calculated for another $cart_hash
if ($REQUEST_METHOD == 'GET') {
    $userinfo = cw_call('cw_checkout_userinfo', array($user_account));
    $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
    $cart_hash = crc32(serialize(array($cart,$user_account,$user_address)));
    $special_offers_apply['cart_hash'] = $cart_hash;
}

$special_offers_apply = array();
$special_offers_apply['free_shipping'] = false;
$special_offers_apply['cart_hash'] = $cart_hash;
// TODO: Do not forget about MDM

$join_statement = $where_statement = '';
$offers = cw_query("SELECT o.offer_id, o.exclusive, o.repeatable, o.pid FROM $tables[ps_offers] o $join_statement WHERE o.active=1 AND o.startdate<UNIX_TIMESTAMP() and (o.enddate+86400)>UNIX_TIMESTAMP() $where_statement ORDER BY priority");

# Delete all free added products
if (is_array($cart['products'])) {
foreach ($cart['products'] as $kk=>$vv) {
	if ($vv["promotion_suite"]["free_product"] == 'Y') cw_call('cw_delete_from_cart', array(&$cart,$vv['cartid']));
	else unset($cart['products'][$kk]['promotion_suite']);
}
}
# / Delete all free added products

// $affected_product_ids is used to track products locked by applied offers
global $affected_product_ids, $_affected_product_ids;

$affected_product_ids = array();

if (!empty($offers) and is_array($offers)) {

    $bonus_subindex = 0; // Subindex is just a suffix required to make repeatable offers unique in offers array

    for($k=0; $k<count($offers); $k++) {

        $b = $offers[$k];
        $bid = $b['offer_id'].'.'.($bonus_subindex++); // Add subindex, e.g. 10->10.2
        
		# Forget the offer if it's exclusive but some other offers already applied
		if ($b['exclusive']=='Y' && !empty($special_offers_apply['supply'])) continue;

		# temporary save the current affected products in case the whole condition is not met
		$_affected_product_ids = $affected_product_ids;
		
		if (cw_special_offer_check($b['offer_id'])) {
			
			$current_bonuses = cw_call('cw_ps_offer_bonuses', array($b['offer_id']));

/*			
          $current_bonuses = cw_query_hash("SELECT b.type, b.apply, b.coupon, b.discount, b.disctype, bd.object_id, bd.quantity, bd.object_type FROM $tables[ps_bonuses] b LEFT JOIN $tables[ps_bonus_details] bd ON b.bonus_id=bd.bonus_id WHERE b.offer_id='$b[offer_id]'",'type',true,false);

            foreach ($current_bonuses as $type=>$bonuses)
                foreach($bonuses as $kk=>$bonus) {
                    if ($type == PS_COUPON) 
                        $current_bonuses[$type][$kk] = array_intersect_key($bonus,array('coupon'=>1));
                    if ($type == PS_FREE_PRODS)
                        $current_bonuses[$type][$kk] = array_intersect_key($bonus,array('object_id'=>1,'quantity'=>1,'object_type'=>1));
                    if ($type == PS_DISCOUNT || $type == PS_FREE_SHIP) {
                        if ($bonus['apply'] != PS_APPLY_PRODS) {
							unset($bonus['object_id'],$bonus['quantity'],$bonus['object_type']);
						}
                        $current_bonuses[$type][$kk] = array_intersect_key($bonus,array('object_id'=>1,'quantity'=>1,'discount'=>1,'disctype'=>1,'apply'=>1,'object_type'=>1));
                    }
                }
*/
			# Add applicable offer info
			$special_offers_apply['supply'][$bid] = $current_bonuses;
			
			#
			# Prepare common array of bonuses including current bonuses
			#
			
			/*
			 * Prepare common free_shipping array
			 */
            $special_offers_apply['free_shipping']['methods'] = cw_array_merge($special_offers_apply['free_shipping']['methods'],$current_bonuses[PS_FREE_SHIP]['methods']);

			// If at least one bonus gives free shipping to whole cart, then free shipping will be applied to cart
			if ($current_bonuses[PS_FREE_SHIP]['apply'] == PS_APPLY_CART) {
				$special_offers_apply['free_shipping']['apply'] = PS_APPLY_CART;
			}
			if (in_array($current_bonuses[PS_FREE_SHIP]['apply'],array(PS_APPLY_COND, PS_APPLY_PRODS)) 
				&& $special_offers_apply['free_shipping']['apply'] != PS_APPLY_CART) {
				$special_offers_apply['free_shipping']['apply'] = $current_bonuses[PS_FREE_SHIP]['apply'];
			}
			$special_offers_apply['free_shipping']['rate'] += $current_bonuses[PS_FREE_SHIP]['discount'];

			// If free shipping applicable to products from condition
			// Possible bug - $_affected_product_ids contains products from previous offers too?
			if ($current_bonuses[PS_FREE_SHIP]['apply'] == PS_APPLY_COND) {
                             if (!empty($_affected_product_ids['ids'])) {
				foreach($_affected_product_ids['ids'] as $pid=>$qty)
					$special_offers_apply['free_shipping']['products'][$pid] += ($qty-$affected_product_ids['ids'][$pid]);
                             }
			}
	
			// If free shipping applicable to selected products
			if ($current_bonuses[PS_FREE_SHIP]['apply'] == PS_APPLY_PRODS) {
				
				// Check all free-shipping bonuses of the offer
				foreach($current_bonuses[PS_FREE_SHIP]['products'] as $pid=>$qty) {
					$special_offers_apply['free_shipping']['products'][$pid] += $qty;
				}

				foreach($current_bonuses[PS_FREE_SHIP]['categories'] as $cid=>$qty) {

					foreach ($cart['products'] as $kk=>$vv) {
						
						// Detect if product is from required category or its subcategories
						$pcats = Product\Category\get($vv['product_id']);
						$parents = cw_category_get_path(array_column($pcats, 'category_id'));
						$is_parent = in_array($cid, $parents);
						
						if ($is_parent) {
							$special_offers_apply['free_shipping']['products'][$vv['product_id']] += min($vv['amount'], $qty);
							$qty -= min($vv['amount'], $qty);
						}
					}
				
				}
			}
			/*
			 * END: Prepare common free_shipping array
			 */

			/*
			 * Prepare common free_products array
			 */			 
			// Re-collect free products array
			if (!empty($current_bonuses[PS_FREE_PRODS])) {
				foreach ($current_bonuses[PS_FREE_PRODS] as $pid=>$qty) {
					$special_offers_apply['free_products'][$pid] += $qty;
				}
			}
			/*
			 * END: Prepare common free_products array
			 */		
			 			
			// Update set of affected products.
			// Discount Bundles do not capture products, so products could be used later by regular offers
			if (empty($b['pid'])) $affected_product_ids = $_affected_product_ids;

			// Exclusive offers 
			if ($b['exclusive']=='Y') break;

            $offers[$k]['repeatable']--;
            $has_repeatable_conditions = cw_query_first_cell("SELECT count(cond_id) FROM $tables[ps_cond_details] WHERE offer_id='$b[offer_id]' AND object_type IN ('".PS_OBJ_TYPE_MANS."','".PS_OBJ_TYPE_CATS."','".PS_OBJ_TYPE_PRODS."','".PS_OBJ_TYPE_ATTR."') AND quantity>0");

            if ($has_repeatable_conditions && $offers[$k]['repeatable'] > 0) {
                // Repeat this offer again
                array_splice($offers,$k,0,array($offers[$k]));
            };
            			
		}
	}

}
if ($_GET['showmedebug']=='Y') {
	cw_var_dump($special_offers_apply);
}
unset($affected_product_ids, $_affected_product_ids, $offers);
