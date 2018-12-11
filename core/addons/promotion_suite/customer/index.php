<?php 

if (!isset($addons['promotion_suite'])) {
    return;
}
    
if (AREA_TYPE != 'C') {
	return;
}

$smarty->assign('ps_img_type', PS_IMG_TYPE);

// get the featured offer
$smarty->assign('ps_featured_offer', cw_ps_get_featured_offer());

return;
