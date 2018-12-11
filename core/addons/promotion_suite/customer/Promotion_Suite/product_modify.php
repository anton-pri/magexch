<?php
// CartWorks.com - Promotion Suite 

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }
if (empty($addons["Promotion_Suite"])) return;

#
# Define data for the navigation within section
#
$dialog_tools_data["left"][] = array("link" => $pm_link."&section=discountbundles", "title" => cw_get_langvar_by_name("lbl_discount_bundles"));

if ($REQUEST_METHOD=="POST") {
	if ($mode == 'discountbundles') {

			$bonusid = cw_query_first_cell("SELECT bonusid FROM $tables[bonuses] WHERE pid='$productid' AND bonus_active='Y'");
			if (empty($bonusid)) {
				#
				# Create new bonus
				#
				db_query("INSERT INTO $tables[bonuses] (bonus_name, bonus_active, start_date, end_date, exclusive, priority, pos, pid, auto) VALUES ('Product #$productid', 'Y', UNIX_TIMESTAMP(), 9999999999, 'N', -1, -1, '$productid', 'N')");
				$bonusid = db_insert_id();
				$query_data = array(
					"bonusid" => $bonusid,
					"type" => 'P',
					"value" => '0.00',
					"objid" => $productid,
					"quantity" => '1'
				);
				cw_array2insert("bonus_conditions", $query_data);	
				
				$top_message["content"] = cw_get_langvar_by_name("msg_new_bonus_created");
			}
			else {
				$top_message["content"] = cw_get_langvar_by_name("msg_bonus_upd");
			}
			
			db_query("UPDATE $tables[bonuses] SET auto='N' WHERE bonusid='$bonusid'");
			
			if (!empty($del_cond)) {
				foreach ($del_cond as $k=>$v) {
					$k=intval($k);
					db_query("DELETE FROM $tables[bonus_conditions] WHERE conditionid='$k'");
				}
			}
			# Add new selected product to conditions
			if (!empty($new_P_id) && is_array($new_P_id)) {
				foreach ($new_P_id as $k=>$v) {
					if ($v == $productid) {$top_message["content"] = "Warning! Product #$productid cannot be added to its own buytogether package."; $top_message["type"]='W';continue;}
					if (!empty($v) && !empty($new_P_quantity[$k])) {
					$query_data = array(
					"bonusid" => $bonusid,
					"type" => 'P',
					"value" => '0.00',
					"objid" => $v,
					"quantity" => $new_P_quantity[$k]
					);
					cw_array2insert("bonus_conditions", $query_data);	
					}
				}
			}
			# Copy conditions products to discount bonus
			$products = cw_query_column("SELECT objid FROM $tables[bonus_conditions] WHERE type='P' AND bonusid='$bonusid'");
			if (count($products)>1) {
				# There are additional products in kit - complete bonus creation.
				foreach ($products as $pid) {
					$sup['D']['product'][$pid] = array('pid'=>$pid);
				}
				$sup['D']['type'] = 'S';
				$query_data = array(
					"bonusid" => $bonusid,
					"type" => 'D',
					"data" => serialize($sup['D'])
				);
				cw_array2insert("bonus_supply", $query_data, true);				
			} else {
				# There are no products in kit except main product - delete kit.
				cw_delete_special_offer($bonusid);
			}
		cw_refresh("discountbundles");
	}
} elseif ($section=='discountbundles') {
	$bonusid = cw_query_first_cell("SELECT bonusid FROM $tables[bonuses] WHERE pid='$productid' AND bonus_active='Y'");
    if (empty($tables['products_lng_current'])) $tables['products_lng_current']=$tables['products']; // products_lng_current exists in 4.5.x only
	$bonus_details['P'] = cw_query("SELECT conditionid, objid, quantity, product FROM $tables[bonus_conditions] bc LEFT JOIN $tables[products_lng_current] prod ON bc.objid=prod.productid WHERE bonusid='$bonusid' AND type='P' AND objid!='$productid'");
	$bonus_details['sup']['D'] = unserialize(cw_query_first_cell("SELECT data FROM $tables[bonus_supply] WHERE bonusid='$bonusid' AND type='D'"));
	$smarty->assign("bonus_details", $bonus_details);
	
}

// CartWorks.com - Promotion Suite 
?>
