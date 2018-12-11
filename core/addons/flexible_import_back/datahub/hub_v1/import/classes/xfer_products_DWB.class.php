<?php
/**
 ALTER TABLE xfer_products_DWB ADD INDEX ( `catalogid` );
ALTER TABLE  xfer_products_DWB ADD INDEX (  `cstock` );
ALTER TABLE  xfer_products_DWB ADD INDEX (  `avail_code` ); 
ALTER TABLE  xfer_products_DWB ADD INDEX (  `cvintage` ); 
ALTER TABLE  xfer_products_DWB ADD INDEX (  `mfg` ); 
 ALTER TABLE `xfer_products_DWB` ADD INDEX ( `ccode` );
 
 ALTER TABLE `xfer_products_DWB` ADD UNIQUE (`catalogid`); 
 */


//new sql
//ALTER TABLE  `xfer_products_DWB` ADD  `ctwelvebottleprice` DECIMAL( 19, 4 ) NOT NULL DEFAULT  '0.0000' AFTER  `cprice`;
//ALTER TABLE  `xfer_products_DWB` ADD  `ctwelvebottlecost` DECIMAL( 19, 4 ) NOT NULL DEFAULT  '0.0000' AFTER  `Cost`;
//ALTER TABLE `site_updates` ADD `ctwelvebottleprice` DECIMAL( 7, 2 ) NULL AFTER `cprice` ;
//ALTER TABLE `site_updates` ADD `ctwelvebottlecost` DECIMAL( 7, 2 ) NULL AFTER `Cost` ;

class xfer_products_DWB extends feed {
	public static $table = 'xfer_products_DWB';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  hub_delete_xfer_products_DWB	
	public static function  hub_delete_xfer_products_DWB() {
		$sql = "DELETE FROM " . self::table_name();
		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  hub_insert_xfer_from_store_DWB	
//this gives more records in mysql
	public static function hub_insert_xfer_from_store_DWB() {
		$sql = "INSERT INTO xfer_products_DWB ( catalogid, cstock, sku, ccode, cost, avail_code )
						SELECT sku_item.item_id AS catalogid, sku_item.qty AS cstock, sku_item.ssku, CONCAT(\"dwb\" , qs.`Item #`) AS ccode, qs.Cost, COALESCE(sku_item.aavail_code,'1') AS avail_code
						FROM ((select si.item_id, sf.`Avail Qty` as qty, min(sf.`Item #`) as ssku, min(sf.`DWB Avail Code`) as aavail_code
						from DWB_store_feed sf
						inner join item_store2 si on (sf.`Item #` = si.store_sku and si.store_id = 2)
						group by si.item_id, sf.`Avail Qty`) AS sku_item INNER JOIN (select si2.item_id, max(DWB_store_feed.`Avail Qty`) as nqty
						   from DWB_store_feed
						   inner join item_store2 si2 on (DWB_store_feed.`Item #` = si2.store_sku and si2.store_id = '2')
						   group by si2.item_id) AS item_qty ON (sku_item.item_id = item_qty.item_id) AND (sku_item.qty = item_qty.nqty)) 
						   INNER JOIN DWB_store_feed AS qs ON sku_item.ssku = qs.`Item #`";
		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  hub_update_xfer_from_item_DWB
	public static function hub_update_xfer_from_item_DWB() {
		$sql = 'OPTIMIZE table ' . self::table_name();
		mysql_query($sql) or sql_error($sql);			
		$sql = "UPDATE (xfer_products_DWB AS x INNER JOIN item ON x.catalogid = item.id) LEFT JOIN item_price AS pr ON item.id = pr.item_id and pr.store_id = '2' 
						SET x.cdescription = COALESCE(item.LongDesc,' '),
						x.cimageurl = COALESCE(item.cimageurl,' '),
						x.mfg = COALESCE(item.Producer,' '),
						x.cvintage = COALESCE(item.Vintage,' '),
						x.vname = COALESCE(item.varietal,' '),
						x.name = COALESCE(item.name,' '),
						x.country = COALESCE(item.country,' '),
						x.cregion = COALESCE(item.Region,' '),
						x.appellation = COALESCE(item.Appellation,' '),
						x.subappellation = COALESCE(item.`sub-appellation`,' '),
						x.csize = COALESCE(item.Size,' '),
						x.RobertParkerRating = COALESCE(item.`RP Rating`,''),
						x.RobertParkerReview = COALESCE(item.`RP Review`,''),
						x.WineSpectatorRating = item.`WS Rating`,
						x.WineSpectatorReview = COALESCE(item.`WS Review`,''),
						x.WineEnthusiaisRating = item.`WE Rating`,
						x.WineEnthusiastReview = item.`WE Review`,
						x.StephenTanzerRating = item.`ST Rating`,
						x.StepehnTanzerReview = COALESCE(item.`ST Review`,' '),
						x.DecanterRating = item.`DC Rating`,
						x.DecanterReview = item.`DC Review`,
						x.BeverageTastingInstituteRating = item.`BTI Rating`,
						x.BeverageTastingInstituteReview = item.`BTI Review`,
						x.WineSpiritsRating = item.`W&S Rating`,
						x.WineSpiritsReview = item.`W&S Review`,
						x.WineryReview = item.`Winery Review`,
						x.cprice = COALESCE(pr.price,'0'),
						x.weight = COALESCE(item.TareWeight,'3'),
						x.keywords = COALESCE(item.keywords,''),
						x.extendedimage = COALESCE(item.extendedimage,'')";
		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  hub_set_hide_true_DWB
	public static function hub_set_hide_true_DWB() {
		$sql = "UPDATE " . self::table_name() . " SET hide = '1'";
		mysql_query($sql) or sql_error($sql);				
	}
	
//Equivalent:  hub_set_status_for_avail_code_0_DWB
	public static function hub_set_status_for_avail_code_0_DWB() {
//		$sql = "UPDATE " . self::table_name() . " AS s 
//						SET s.hide = '0'
//						WHERE (s.avail_code = 0 and s.cstock > 0)";
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0',
						s.avail_code = '1'
						WHERE (s.avail_code = '0' and s.cstock > 0)";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_set_status_for_avail_code_1_DWB
	public static function hub_set_status_for_avail_code_1_DWB() {
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0', 
						s.minimumquantity = Null
						WHERE (s.avail_code = '1' and s.cstock > 0)";
		mysql_query($sql) or sql_error($sql);				
	}	
//Equivalent:  hub_set_status_for_avail_code_2_DWB
	public static function hub_set_status_for_avail_code_2_DWB() {
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0'
						WHERE s.avail_code = '2'";
		mysql_query($sql) or sql_error($sql);				
	}

//Equivalent:  hub_set_status_for_avail_code_3_DWB
	public static function hub_set_status_for_avail_code_3_DWB() {
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0'
						WHERE s.avail_code = '3'";
		mysql_query($sql) or sql_error($sql);				
	}
		
//Equivalent:  hub_set_status_for_avail_code_4_DWB
	public static function hub_set_status_for_avail_code_4_DWB() {
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0'
						WHERE s.avail_code = '4'";
		mysql_query($sql) or sql_error($sql);				
	}	
	
//Equivalent:  hub_set_hide_true_for_certain_products_DWB	
	public static function hub_set_hide_true_for_certain_products_DWB() {
		$sql = "UPDATE " . self::table_name() . " AS x, hub_config AS c 
						SET x.hide = IF((x.avail_code = '2' and c.display_feed_items_DWB = False) or  Instr(COALESCE(c.bottle_size_do_not_display,' '),x.csize)
						or COALESCE(x.mfg,'') = '' or COALESCE(x.name,'') = '', True, x.hide)";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  hub_DWB_hide_no_vintage_if_similar_with_vintage
	public static function hub_DWB_hide_no_vintage_if_similar_with_vintage() {
		$sql = "UPDATE " . self::table_name() . " AS x 
						INNER JOIN (SELECT mfg, name, vname, csize 
						from xfer_products_DWB 
						where (Trim(COALESCE(cvintage,'')) <> '' and hide = False)) AS y 
						ON (x.csize = y.csize) AND (x.vname = y.vname) AND (x.name = y.name) AND (x.mfg = y.mfg) 
						SET x.hide = true
						WHERE (isnull(x.cvintage) or Trim(x.cvintage) = '')";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_hide_price_less_than_3_DWB	
	public static function hub_hide_price_less_than_3_DWB() {
		$sql = "UPDATE " . self::table_name() . "
						SET hide = True
						WHERE cprice < 3 or cost = '0'";
		mysql_query($sql) or sql_error($sql);					
	}
//Equivalent:  hub_set_min_qty_price_threshold_DWB	
	public static function hub_set_min_qty_price_threshold_DWB() {
		$sql = "UPDATE (select * from hub_price_settings where store_id = '2') AS s, 
						" . self::table_name() . " AS x 
						INNER JOIN item_store2 AS i ON x.catalogid = i.item_id and i.store_id = '2' 
						SET x.minimumquantity = IF( x.cost <= s.SWE_cost_threshold and x.avail_code = '2' and x.cstock <= 0, 
						SWE_min_qty_under_cost_threshold,Null)";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  hub_update_xfer_from_feeds_DWB	
	public static function hub_update_xfer_from_feeds_DWB() {
		$sql = "INSERT INTO xfer_products_DWB (catalogid, ccode, Cost, avail_code)
						SELECT a.catalogid,  IF(Trim(COALESCE(x.ccode,'')) = '',a.xref,x.ccode), 
						IF(COALESCE(x.Cost,0) = 0, 
						a.cost,x.Cost), 
						IF(COALESCE(x.avail_code, 0) > -1 and COALESCE(x.avail_code, 0) < 3,IF(a.cstock > 0,2,1),COALESCE(x.avail_code, 0))
						FROM xfer_products_DWB AS x RIGHT JOIN xfer_temp AS a ON x.catalogid = a.catalogid
						ON DUPLICATE KEY UPDATE 
						catalogid = a.catalogid, 
						ccode = IF(Trim(COALESCE(x.ccode,'')) = '',a.xref,x.ccode), 
						Cost = IF(COALESCE(x.Cost,0) = 0, a.cost,x.Cost), 
						avail_code = IF(COALESCE(x.avail_code, 0) > -1 and COALESCE(x.avail_code, 0) < 3,IF(a.cstock > 0,2,1),COALESCE(x.avail_code, 0))";
		mysql_query($sql) or sql_error($sql);

		$sql = "UPDATE xfer_products_DWB AS x
						INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '2'
						SET 
						x.avail_code = IF(COALESCE(x.avail_code, 0) > -1 and COALESCE(x.avail_code, 0) < 3,IF(pr.stock > 0,2,x.avail_code),COALESCE(x.avail_code, 0))
						 
						WHERE 
						COALESCE(x.sku, '') = '' 
						AND ( COALESCE(pr.store_sku, '') <> '' AND COALESCE(pr.item_id, 0) <> 0 AND COALESCE(pr.xref, '') <> '' AND COALESCE(pr.price, 0) <> 0 AND COALESCE(pr.supplier_id, 0) <> 0 AND COALESCE(pr.stock, 0) <> 0 AND COALESCE(pr.cost, 0) <> 0)
						";		
		mysql_query($sql) or sql_error($sql);			

	}
	
	public static function hub_update_xfer_from_feeds_DWB_insert() {
		$sql = "select x.*, x.Cost as cost, a.catalogid as acatalogid, a.xref as axref, a.cost as acost, a.cstock as acstock
					  from xfer_products_DWB AS x
						RIGHT JOIN xfer_temp AS a ON x.catalogid = a.catalogid 
						where coalesce(x.catalogid, '') = ''";
		$result = mysql_query($sql) or sql_error($sql);		
	 	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	 		if(is_null($row['ccode']) || empty($row['ccode'])) {
	 			$row['ccode'] = $row['axref'];
	 		}	 		

	 		if(is_null($row['cost']) || empty($row['cost'])) {
	 			$row['cost'] = $row['acost'];
	 		}
	 		 		
	 		if($row['avail_code'] > -1 && $row['avail_code'] < 3) {
	 			if($row['acstock'] > 0) {
	 				$row['avail_code'] = 2;
	 			}
	 			else {
	 				$row['avail_code'] = 1;	 			
	 			}
	 		}
	 		$sql = "INSERT INTO xfer_products_DWB (catalogid, ccode, cost, avail_code)
	 						VALUES (
	 							'{$row['acatalogid']}', 
	 							'{$row['ccode']}', 
	 							'{$row['cost']}',
	 							'{$row['avail_code']}'
	 							)";
			mysql_query($sql) or sql_error($sql);		 		
	 	}
	}
//Equivalent:  update_xfer_manual_price
	public static function update_xfer_manual_price() {
//		$sql = "UPDATE xfer_products_DWB AS xfer 
//						INNER JOIN dwb_manual_price AS price 
//						ON price.catalogid =  xfer.catalogid 
//						SET xfer.cprice = IF(COALESCE(price.manual_price, '') <> '', price.manual_price,  xfer.cprice), 
//						xfer.avail_code = IF(COALESCE(price.avail_code, '') <> '', 
//						price.avail_code,  xfer.avail_code)";
		$sql = "UPDATE xfer_products_DWB AS xfer 
						INNER JOIN dwb_manual_price AS price 
						ON price.catalogid =  xfer.catalogid 
						SET xfer.cprice = IF(COALESCE(price.manual_price, 0) <= 0, xfer.cprice ,  price.manual_price), 
						xfer.avail_code = IF(COALESCE(price.avail_code, '') <> '', price.avail_code,  xfer.avail_code)";	
		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  update_hide_xfer_products_DWB
	public static function update_hide_xfer_products_DWB() {
	//changed May 11, 2011 from this to the below
//		$sql = "UPDATE xfer_products_DWB 
//						SET hide = IF(avail_code = -1, '1', '0')";
		////SET hide = IF(avail_code = -1, TRUE, hide)";
		
		$sql = "UPDATE xfer_products_DWB as x
						SET x.hide = IF(avail_code = -1, '1', x.hide)";		
		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function set_supplierid() {
		$sql = "UPDATE xfer_products_DWB as x
						INNER JOIN item_xref as i ON x.catalogid = i.item_id
						SET x.supplierid = COALESCE(i.supplier_id, x.supplierid)
						WHERE i.store_id = 2";
		mysql_query($sql) or sql_error($sql);	

		$sql = "UPDATE xfer_products_DWB AS x
						INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '2'
						SET 
						x.cprice =  COALESCE(pr.price, x.cprice), 
						x.Cost = IF(x.cstock = 0, COALESCE(pr.cost, x.Cost), x.Cost), 
						x.ccode = COALESCE(pr.xref, x.ccode),
						x.supplierid = COALESCE(pr.supplier_id, x.supplierid)	 
						WHERE 
						COALESCE(x.sku, '') = '' 
						AND ( COALESCE(pr.store_sku, '') = '' AND COALESCE(pr.item_id, 0) <> 0 AND COALESCE(pr.xref, '') <> '' AND COALESCE(pr.price, 0) <> 0 AND COALESCE(pr.supplier_id, 0) <> 0 AND COALESCE(pr.stock, 0) <> 0 AND COALESCE(pr.cost, 0) <> 0)
						AND x.hide = 0";
		mysql_query($sql) or sql_error($sql);			
	}
	
	public static function update_twelve_bot_price_DWB() {
		$sql = "UPDATE (xfer_products_DWB AS x INNER JOIN item ON x.catalogid = item.id) 
						LEFT JOIN item_price_twelve_bottle AS pr ON item.id = pr.item_id and pr.store_id = '2' 
						SET 
						x.ctwelvebottleprice  = COALESCE(pr.price,'0'),
						x.ctwelvebottlecost  = COALESCE(pr.cost,'0')";
		mysql_query($sql) or sql_error($sql);	
			
		$sql = "UPDATE xfer_products_DWB 
						SET ctwelvebottleprice = 0
						WHERE cprice <= ctwelvebottleprice
						AND COALESCE(cprice, 0) > 0";
		mysql_query($sql) or sql_error($sql);		
	}	
	/**
	 * 
	 * @todo add other conditions to set ctwelvebottleprice to 0
	 */
	public static function set_twelvebot_price_to_zero() {
		$sql = "UPDATE xfer_products_DWB 
						SET ctwelvebottleprice = 0
						WHERE avail_code = '1' 
						AND cstock < 12";
		mysql_query($sql) or sql_error($sql);			
	}		
	public function set_twelvebot_cost_to_zero() {
		$sql = "UPDATE xfer_products_DWB 
						SET ctwelvebottlecost = 0
						WHERE cprice <= 0 OR ctwelvebottleprice <= 0";
		mysql_query($sql) or sql_error($sql);			
	}	

	//this field doesn't exist yet in xfer_products_DWB
	public function update_meta_description() {
//		$sql = "UPDATE (xfer_products_DWB AS x INNER JOIN item as i ON x.catalogid = i.ID)    
//						SET x.meta_description  =  
//							CONCAT(
//								'Buy ',
//								IF(COALESCE(i.Producer, '') <> '', CONCAT(i.Producer, ' '),  ''),
//								IF(COALESCE(i.name, '') <> '', CONCAT(i.name, ' '),  ''),
//								IF(COALESCE(i.Vintage, '') <> '', CONCAT(i.Vintage, ' '),  ''),	
//								IF(COALESCE(i.Size, '') <> '', CONCAT(i.Size, ' '),  ''),
//								'from ',
//								IF(COALESCE(i.`sub-appellation`, '') <> '', CONCAT(i.`sub-appellation`, ' '),  ''),	
//								IF(COALESCE(i.Appellation, '') <> '', CONCAT(i.Appellation, ' '),  ''),		
//								IF(COALESCE(i.Region, '') <> '', CONCAT(i.Region, ' '),  ''),		
//								IF(COALESCE(i.Country, '') <> '', CONCAT(i.Country, ' '),  ''),	
//								'at discount prices on www.discountwinebuys.com'
//							)
//						WHERE x.hide = 0";
//		mysql_query($sql) or sql_error($sql);	
	}
	
	public function update_keywords() {	
		$sql = "UPDATE (xfer_products_DWB AS x INNER JOIN item as i ON x.catalogid = i.ID)    
						SET x.keywords  =  
							CONCAT(
									CONCAT(
										'buy,',
										IF(COALESCE(i.Producer, '') <> '', CONCAT(i.Producer, ','),  ''),
										IF(COALESCE(i.name, '') <> '', CONCAT(i.name, ','),  ''),
										IF(COALESCE(i.Vintage, '') <> '', CONCAT(i.Vintage, ','),  ''),	
										IF(COALESCE(i.Size, '') <> '', CONCAT(i.Size, ','),  ','),								
										IF(COALESCE(i.`sub-appellation`, '') <> '', CONCAT(i.`sub-appellation`, ','),  ''),	
										IF(COALESCE(i.Appellation, '') <> '', CONCAT(i.Appellation, ','),  ''),		
										IF(COALESCE(i.Region, '') <> '', CONCAT(i.Region, ','),  ''),		
										IF(COALESCE(i.Country, '') <> '', CONCAT(i.Country, ','),  '')							 
									),
									'buy wine online,',
									'discount prices,',
									'www.discountwinebuys.com'		
							)

						WHERE x.hide = 0 AND COALESCE(x.keywords, '') = ''";		
		mysql_query($sql) or sql_error($sql);							
	}	
}//end class	