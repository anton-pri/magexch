<?php
// CartWorks.com - Promotion Suite 

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }
if (empty($addons["Promotion_Suite"])) return;

// This setting was cut-off from the new version.
// Show recently viewed products among all visitors - quite useless. The feature was not demanded
if ($config['Promotion_Suite']['recently_viewed_enable']!='Y') return;

	if (empty($cat)) {
		if (!empty($product_added)) $productid = $product_added;
		if (!empty($productid)) $cat_ps = cw_query_first_cell("SELECT categoryid FROM $tables[products_categories] WHERE main='Y' AND productid='$productid'");
	} else { 
		$cat_ps = $cat; 
	}

	if (!empty($cat_ps)) {
x_load('product');
		$recent_pids = cw_query_column("select s.id FROM xcart_stats_shop s, xcart_products_categories c WHERE s.action='V' AND s.id=c.productid AND c.categoryid='$cat_ps' AND s.id!='$productid' order by s.date desc limit ".$config['Promotion_Suite']['recently_viewed_items']*3);

		$recent_pids = array_slice(array_unique($recent_pids),0,$config['Promotion_Suite']['recently_viewed_items']);

		$pids_query['where'][] = $tables['products'].".productid IN ('".implode($recent_pids,"','")."')";
		if (!empty($recent_pids)) $recent_products = cw_search_products($pids_query,$userinfo['membershipid']);
		$smarty->assign('recent_products',$recent_products);
		unset($recent_products, $recent_pids, $pids_query);
	}



// CartWorks.com - Promotion Suite 
?>
