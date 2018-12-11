<?php
// CartWorks.com - Promotion Suite 

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

#
# Create coupon 
#

# Remember, this piece of code is being executed from function context.
# Do not forget decalre global variables.

global $special_offers_apply;
x_session_register("special_offers_apply");

if (!empty($special_offers_apply['supply'])) {

	foreach ($special_offers_apply['supply'] as $v) {
		
		if ($v['C']['status']!='A') continue;
                if ($v['C']['discount']==0.00 && $v['C']['coupon_type']!='free_ship') continue;

		
		# Create new coupon from template stored in bonus
		$coupon_id = substr(strtoupper(md5(uniqid(rand()))),0,16);
		$v['C']['status'] = 'D';
		$v['C']['expire'] = $v['C']['expire']-$config["Appearance"]["timezone_offset"];

		$how_to_apply_p = $v['C']['how_to_apply_p'];
		$how_to_apply_c = $v['C']['how_to_apply_c'];
			
		$apply_category_once = $apply_product_once = "N";
		switch ($v['C']['apply_to']) {
		case '':
		case 'any':
			$v['C']['productid'] = 0;
			$v['C']['categoryid'] = 0;
			break;
		case 'product':
			$v['C']['minimum'] = 0;
			$v['C']['categoryid']=0;
			$apply_product_once = $how_to_apply_p;
			break;
		case 'category':
			$v['C']['minimum'] = 0;
			$v['C']['productid']=0;
			if ($how_to_apply_c == "Y") {
				$apply_product_once = $apply_category_once = "Y";
			}
			elseif ($how_to_apply_c == "N1") {
				$apply_product_once = "N";
				$apply_category_once = "N";
			}
			else {
				$apply_product_once = "Y";
				$apply_category_once = "N";
			}
			break;
		}
				
		if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[discount_coupons] WHERE coupon='$coupon_id'") > 0) {
			$top_message["content"] = cw_get_langvar_by_name("msg_err_discount_coupons_add");
			$top_message["type"] = "E";
		}
		else {
			$c = $v['C'];
			db_query("INSERT INTO $tables[discount_coupons] (coupon, discount, coupon_type, minimum, times, per_user, expire, status, provider, productid, categoryid, recursive, apply_category_once, apply_product_once) VALUES ('$coupon_id', '$c[discount]', '$c[coupon_type]', '$c[minimum]', '$c[times]', '$c[per_user]', '$c[expire]', '$c[status]', '$login', '$c[productid]', '$c[categoryid]', '$c[recursive]', '$apply_category_once', '$apply_product_once')");
			$special_offers_apply['coupons'][$coupon_id] = $c;
		}
		
	}

	$extras['special_offers_apply'] = serialize($special_offers_apply);
}

// CartWorks.com - Promotion Suite 
?>
