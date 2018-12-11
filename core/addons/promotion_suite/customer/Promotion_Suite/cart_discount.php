<?php
// CartWorks.com - Promotion Suite 

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

#
# Apply special bonus discount instead of default discount if it is greater than default.
# This routine for the special bonus discounts only which must be applied to whole cart
#
# Remember, this piece of code is being executed from function context.
# Do not forget decalre global variables.
global $special_offers_apply;
$current_discount = 0;
if (!empty($special_offers_apply['supply'])) {

	foreach ($special_offers_apply['supply'] as $sup) {

		if ($sup['D']['type']=='Y')
			$current_discount = ($sup['D']['discount_type']=="percent")?$avail_discount_total*$sup['D']['discount']/100:$sup['D']['discount'];
		else
			continue;
		
		if ($current_discount > $discount_info['max_discount']) { # hey, we've found better discount
			$discount_info = array (
				'discount_type'	=> $sup['D']['discount_type'],
				'discount'		=> $sup['D']['discount'],
				'max_discount'	=> $current_discount
			);
			$special_offers_apply['discount'] = $discount_info;
		}
		
	}
	
}

// CartWorks.com - Promotion Suite 
?>
