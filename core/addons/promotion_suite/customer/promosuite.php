<?php 

if (!isset($addons['promotion_suite'])) {
    return;
}
    
if (AREA_TYPE != 'C') {
	return;
}


$location[] = array(cw_get_langvar_by_name('lbl_ps_cust_offers'), '');

$smarty->assign('main', 'promosuite');
$smarty->assign('ps_img_type', PS_IMG_TYPE);

// get the list of the available offers
$smarty->assign('ps_offers', cw_ps_get_offers());

return;
