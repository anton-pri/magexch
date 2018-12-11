<?php
// CartWorks.com - Promotion Suite

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

if (empty($addons["Promotion_Suite"])) return;

if ($config['Promotion_Suite']['display_offers_cat'] != 'Y' && $config['Promotion_Suite']['display_offers_product']!='Y') return;

include_once $xcart_dir.'/addons/Promotion_Suite/user_bonuses.php';

$bonuses = $user_bonuses;

if ($_GET['showmedebug']=='Y') {
        x_load("debug");
        $zones = cw_get_customer_zones_avail($userinfo,"");
		echo 'Promotion Suite addon ',PS_VERSION," \n<br />";
		echo __FILE__,' : Customer $zones, List of appropriated $bonuses before zones and categories filter';
        cw_print_r($zones,$bonuses);
}

/*
	For current category show first offer, where this category mentioned in conditions. If there are no such offers, then show first offer.
*/

if (!empty($cat) && !empty($bonuses) && empty($productid)) {
	if ($config['Promotion_Suite']['display_offers_cat'] != 'Y') return;
	$featured = array();
                $pos = cw_query_first("SELECT lpos, rpos FROM $tables[categories] WHERE categoryid='$cat'");
                $lpos = $pos['lpos'];
                $rpos = $pos['rpos'];
                $all_parents = cw_query_column("SELECT categoryid FROM $tables[categories] WHERE $lpos BETWEEN lpos AND rpos");

		if ($config['Promotion_Suite']['also_look_in_subcategories']=='Y') {
			$all_children = cw_query_column("SELECT categoryid FROM $tables[categories] WHERE lpos BETWEEN $lpos AND $rpos");
			if (!empty($all_children))
				$all_parents = array_merge($all_parents,$all_children);
		}

	foreach ($bonuses as $k=>$v) {
		$bonus_with_cat = cw_query_first_cell("SELECT COUNT(conditionid) FROM $tables[bonus_conditions] 
			WHERE bonusid='$v[bonusid]' AND type='C' and objid IN ('".join("','",$all_parents)."')");
		if ($bonus_with_cat) {
			$featured[] = $v;
			if (count($featured)==$config['Promotion_Suite']['display_offers_per_category']) break;
		}
	}
	if (!empty($featured)) $bonuses = $featured;
	elseif ($config['Promotion_Suite']['display_default_offer']=='Y') $bonuses = array_slice($bonuses,0,1);
	else return;
}

if (!empty($productid) && !empty($bonuses)) {
        if ($config['Promotion_Suite']['display_offers_product'] != 'Y') {
		return;
	} else {
		$bids = cw_query_column("SELECT bonusid FROM $tables[bonus_conditions] WHERE type='P' AND objid='$productid' AND bonusid IN ('".join("','",array_keys($user_bonuses))."')");	
        	foreach ($bonuses as $k=>$v) {
			if (!in_array($v['bonusid'],$bids)) unset($bonuses[$k]);
	        }
	}
}
	$smarty->assign("bonuses", $bonuses);

// CartWorks.com - Promotion Suite 
?>
