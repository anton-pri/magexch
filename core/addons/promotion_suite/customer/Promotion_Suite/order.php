<?php
// CartWorks.com - Promotion Suite 

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }


#
# Active or disable coupon connected with order
#

# Remember, this piece of code is being executed from function context.
# Do not forget decalre global variables.

$all_bonuses = $order['extra']['special_offers_apply'];
if (!empty($all_bonuses['coupons'])) {
	
	foreach ($all_bonuses['coupons'] as $coupon_id=>$coupon_data) {
		# check if coupon is already used
		if (!in_array(cw_query_first_cell("SELECT status FROM $tables[discount_coupons] WHERE coupon='$coupon_id'"),array('A','D'))) continue;
		
		# check the current order status and change the coupon status accordingly
		$actual_status = cw_query_first_cell("SELECT status FROM $tables[orders] WHERE orderid='$orderid'");
		if (in_array($actual_status,array('P','C'))) {
			$coupon_status = 'A';
		} else {
			$coupon_status = 'D';
		}
		$query = array('status'=>$coupon_status);
		cw_array2update("discount_coupons",$query,"coupon='$coupon_id'");
		
	}
}

// CartWorks.com - Promotion Suite 
?>
