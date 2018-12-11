<?php

if ($REQUEST_METHOD == 'GET') {
	$offers = cw_ps_get_offers(false);
	$smarty->assign('offers', $offers);
	//var_dump($offers);
	if ($contentsection_id) {
		$cms_offers = cw_ab_get_cms_restrictions($contentsection_id,'PS');
		$smarty->assign('cms_offers', array_column($cms_offers, 'object_id'));
	}
	if ($contentsections_filter['offers']) {
		$smarty->assign('cms_offers', $contentsections_filter['offers']);
	}

}
