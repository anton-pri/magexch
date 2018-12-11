<?php
 
require('core/addons/flexible_import/datahub/hub_v1/import/constants.php');

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
					echo "<td>{$record['Custom Field 5']}</td><td>{$record['Item Number']}</td><td>{$record['Alternate Lookup']}</td>";
					echo '</tr>';					
				}			
			}
			echo '<tr>';
			echo "<td colspan=3><h3>The pos table has NOT be been updated.</h3></td>"; 
			echo '</tr>';
			echo '</table>';					
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
							p.MSRP = COALESCE(pr.price, p.MSRP), 
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
							p.MSRP = COALESCE(pr.price, p.MSRP), 
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
		}
		
		//find dups bof

print("<h3>done...</h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");
die;
