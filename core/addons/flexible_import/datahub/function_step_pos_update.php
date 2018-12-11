<?php

function datahub_import_step_pos_update($dh_update_step, $is_web_mode=false) {
    global $tables, $config;

    global $datahub_import_stop4manual;

db_query("update item_store2 is2 inner join pos p on p.`Item Description`='' and p.`Alternate Lookup`=is2.item_id and p.`Item Number`!=is2.store_sku set is2.store_sku=p.`Item Number`");
db_query("replace into item_store2 (store_sku, store_id, item_id) select `Item Number`, 1, `Alternate Lookup` from pos where `Alternate Lookup` not in (select item_id from item_store2) and `Alternate Lookup`!=0 and `Alternate Lookup`!=''");

db_query("UPDATE (item_store2 AS si INNER JOIN pos AS q ON si.store_sku = q.`Item Number`) INNER JOIN item_price AS pr ON si.item_id = pr.item_id and pr.store_id = 1 SET q.`MSRP` = pr.price WHERE si.store_id = 1 AND q.`MSRP`=0");

db_query("update pos p inner join item_xref ix on REPLACE(p.`Custom Field 5`,' ','')=ix.xref and p.`Vendor Code`=0 and ix.supplier_id!=0 set p.`Vendor Code`=ix.supplier_id");
db_query("update pos p inner join Supplier supp on supp.supplier_id=p.`Vendor Code` and p.`Vendor Code`!=0 and p.`Vendor Name`='' set p.`Vendor Name`=supp.SupplierName");

db_query("delete ix.* from item_xref ix where ix.xref like 'swe-%' and ix.xref not in (select replace(`Custom Field 5`,' ','') from pos)");

$dup_check_passed = false;
$datahub_import_stop4manual = false;

if (!function_exists('mysql_query')) { 
    require('core/addons/flexible_import/datahub/hub_v1/import/constants.php');
}
/*
                $sql = "CREATE TABLE IF NOT EXISTS pos_xref LIKE pos";
                mysql_query($sql) or sql_error($sql);
*/

		//find dups bof
		$sql = "DELETE from pos_xref";
		mysql_query($sql) or sql_error($sql);		

		$sql = "insert into pos_xref
						select * from pos";
		mysql_query($sql) or sql_error($sql);		

		$sql = "UPDATE pos_xref AS p
						INNER JOIN xfer_products_SWE AS x ON p.`Alternate Lookup` = x.catalogid  and x.ccode = p.`Custom Field 5`  
						INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku   					
						INNER JOIN Supplier as s ON x.supplierid = s.supplier_id
						SET 					
						p.`Custom Field 5` = COALESCE(x.ccode, p.`Custom Field 5`)
						WHERE 						
						(x.hide = 0 AND COALESCE(x.sku, '') <> '' AND COALESCE(x.ccode, '') <> '' AND COALESCE(x.Cost, 0) <> 0 AND COALESCE(x.cprice, 0) <> 0 AND COALESCE(x.supplierid, 0) <> 0)
						";			
		mysql_query($sql) or sql_error($sql);		

		$sql = "UPDATE pos_xref AS p
						INNER JOIN xfer_products_SWE AS x ON p.`Alternate Lookup` = x.catalogid and p.`Item Number` = x.sku
						INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku						
						INNER JOIN Supplier as s ON x.supplierid = s.supplier_id		
						SET 
						p.`Custom Field 5` = COALESCE(x.ccode, p.`Custom Field 5`)
						where p.`Alternate Lookup`	not in (
						select * from (
							select  p2.`Alternate Lookup` from pos_xref AS p2
													INNER JOIN xfer_products_SWE AS x ON p2.`Alternate Lookup` = x.catalogid  and x.ccode = p2.`Custom Field 5`  
													INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku   				
													INNER JOIN Supplier as s ON x.supplierid = s.supplier_id
													) as x
						
						)
						AND
						(x.hide = 0 AND COALESCE(x.sku, '') <> '' AND COALESCE(x.ccode, '') <> '' AND COALESCE(x.Cost, 0) <> 0 AND COALESCE(x.cprice, 0) <> 0 AND COALESCE(x.supplierid, 0) <> 0)
						";							
		mysql_query($sql) or sql_error($sql);	

		$sql = "SELECT `Custom Field 5` , COUNT( `Custom Field 5` ) AS NumOccurrences
						FROM pos_xref
						GROUP BY `Custom Field 5`
						HAVING (
						COUNT( `Custom Field 5` ) >1
						)";
		$result = mysql_query($sql) or sql_error($sql);	
		if(mysql_num_rows($result) > 0) {
			echo '<table border=1>';
			echo '<tr>';
			echo "<td>Custom Field 5</td><td>Item Number</td><td>Alternate Lookup</td>";
			echo '</tr>';
			while($row = mysql_fetch_assoc($result)) {
				$sql = "SELECT * FROM pos_xref WHERE `Custom Field 5` = '{$row['Custom Field 5']}'";
				$res = mysql_query($sql) or sql_error($sql);	
				while($record = mysql_fetch_assoc($res)) {	
					echo '<tr>';					
					echo "<td><a target='_blank' href='index.php?target=datahub_item_edit&table_name=item_xref&key_field=xref&key_value={$record['Custom Field 5']}' title='Preview and delete item_xref entry'>{$record['Custom Field 5']}</a></td><td><a target='_blank' href='index.php?target=datahub_item_edit&table_name=item_store2&key_field=store_sku&key_value={$record['Item Number']}' title='Preview and delete item_store2 entry'>{$record['Item Number']}</a></td><td>{$record['Alternate Lookup']}</td>";
					echo '</tr>';					
				}			
			}
			echo '<tr>';
			echo "<td colspan=3><h3>The pos table has NOT be been updated.</h3></td>"; 
			echo '</tr>';
			echo '</table>';
db_query("drop table if exists cw_datahub_step12alus");
db_query("create table cw_datahub_step12alus as select `Alternate Lookup` from pos_xref WHERE `Custom Field 5` in (select `Custom Field 5` from (SELECT `Custom Field 5` , COUNT( `Custom Field 5` ) AS NumOccurrences FROM pos_xref GROUP BY `Custom Field 5` HAVING (COUNT( `Custom Field 5` ) >1)) as double_xrefs) and cast(`Alternate Lookup` as signed)!=0"); 				
                        $datahub_import_stop4manual = true;	
		}
		else {
			$sql = "UPDATE pos AS p
							INNER JOIN xfer_products_SWE AS x ON p.`Alternate Lookup` = x.catalogid  and x.ccode = p.`Custom Field 5`  
							INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku   					
							INNER JOIN Supplier as s ON x.supplierid = s.supplier_id
 	 						LEFT JOIN BevAccessFeeds AS f ON f.xref = x.ccode							
							SET 
							p.`Average Unit Cost` = IF(x.cstock = 0, COALESCE(x.Cost, p.`Average Unit Cost`), p.`Average Unit Cost`),
							p.`Regular Price` = IF(x.cstock = 0,  
																		IF(COALESCE(f.xref, '') = '', Round(CAST(x.Cost as DECIMAL(19,4))*1.5,0)-0.01, Round(IF(IsNull(`f`.`case_price`) or f.case_price = 0 or f.case_price <= f.bot_price,`f`.`bot_price`,`f`.`case_price`/`f`.`botpercase`)*1.5,0)-0.01) 
																		,p.`Regular Price`),							
							p.MSRP = COALESCE(p.MSRP, pr.price), 
							p.`Custom Price 1` = COALESCE(pr.price,p.`Custom Price 1`), 
							p.`Custom Field 5` = COALESCE(x.ccode, p.`Custom Field 5`),
							p.`Vendor Name` = COALESCE(s.SupplierName, p.`Vendor Name`),
							p.`Vendor Code` = COALESCE(x.supplierid, p.`Vendor Code`)		 
							WHERE 						
						
							(x.hide = 0 AND COALESCE(x.sku, '') <> '' AND COALESCE(x.ccode, '') <> '' AND COALESCE(x.Cost, 0) <> 0 AND COALESCE(x.cprice, 0) <> 0 AND COALESCE(x.supplierid, 0) <> 0)
							";			
			mysql_query($sql) or sql_error($sql);		
	
			$sql = "UPDATE pos AS p
							INNER JOIN xfer_products_SWE AS x ON p.`Alternate Lookup` = x.catalogid and p.`Item Number` = x.sku
							INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku						
							INNER JOIN Supplier as s ON x.supplierid = s.supplier_id		
 	 						LEFT JOIN BevAccessFeeds AS f ON f.xref = x.ccode								
							SET 
							p.`Average Unit Cost` = IF(x.cstock = 0, COALESCE(x.Cost, p.`Average Unit Cost`), p.`Average Unit Cost`),
							p.`Regular Price` = IF(x.cstock = 0,  
																		IF(COALESCE(f.xref, '') = '', Round(CAST(x.Cost as DECIMAL(19,4))*1.5,0)-0.01, Round(IF(IsNull(`f`.`case_price`) or f.case_price = 0 or f.case_price <= f.bot_price,`f`.`bot_price`,`f`.`case_price`/`f`.`botpercase`)*1.5,0)-0.01) 
																		,p.`Regular Price`),	
							p.MSRP = COALESCE(p.MSRP, pr.price), 
							p.`Custom Price 1` = COALESCE(pr.price, p.`Custom Price 1`), 
							p.`Custom Field 5` = COALESCE(x.ccode, p.`Custom Field 5`),
							p.`Vendor Name` = COALESCE(s.SupplierName, p.`Vendor Name`),
							p.`Vendor Code` = COALESCE(x.supplierid, p.`Vendor Code`)	
							where p.`Alternate Lookup`	not in (
							select * from (
								select  p2.`Alternate Lookup` from pos AS p2
														INNER JOIN xfer_products_SWE AS x ON p2.`Alternate Lookup` = x.catalogid  and x.ccode = p2.`Custom Field 5`  
														INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku   				
														INNER JOIN Supplier as s ON x.supplierid = s.supplier_id
														) as x
							
							)
							AND
							(x.hide = 0 AND COALESCE(x.sku, '') <> '' AND COALESCE(x.ccode, '') <> '' AND COALESCE(x.Cost, 0) <> 0 AND COALESCE(x.cprice, 0) <> 0 AND COALESCE(x.supplierid, 0) <> 0)
							";							
			mysql_query($sql) or sql_error($sql);	
			echo '<br /><br /><h2>POS table updated</h2>';			
                        $dup_check_passed = true;
		}
		
		//find dups bof

if ($is_web_mode) {

    print("<h3>done...</h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");

    if ($redirect_target) {
        if ($dup_check_passed) { 
            cw_header_location("index.php?target=$redirect_target&step12passed=Y");
        }
    } else { 
        die;
    }
} else {
    return -1;
}

}
