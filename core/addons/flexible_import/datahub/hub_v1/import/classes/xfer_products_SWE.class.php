<?php
/**
 ALTER TABLE `xfer_products_SWE` ADD INDEX ( `catalogid` );
ALTER TABLE  `xfer_products_SWE` ADD INDEX (  `cstock` );
ALTER TABLE  `xfer_products_SWE` ADD INDEX (  `avail_code` ); 
ALTER TABLE  `xfer_products_SWE` ADD INDEX (  `cvintage` ); 
ALTER TABLE  `xfer_products_SWE` ADD INDEX (  `mfg` ); 
ALTER TABLE  `xfer_products_SWE` ADD INDEX (  `ccode` );

 ALTER TABLE `xfer_products_SWE` ADD UNIQUE (`catalogid`);
 */

class xfer_products_SWE extends feed {
	public static $table = 'xfer_products_SWE';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  hub_delete_xfer_products_SWE	
	public static function  hub_delete_xfer_products_SWE() {
		$sql = "DELETE FROM " . self::table_name();
		mysql_query($sql) or sql_error($sql);					
	}
	
	public static function optimize() {
		$sql = 'OPTIMIZE table ' . self::table_name();
		mysql_query($sql) or sql_error($sql);	 				
	}	
	
//Equivalent:  hub_insert_xfer_from_store_SWE //needs POS
//original query doesn't seem to work
//so take the 2 sub-queries and make them into temp tables, populate them and then insert
	public static function hub_insert_xfer_from_store_SWE() {
		//$sql = "DELETE FROM sku_item";
		$sql = "DROP TEMPORARY TABLE IF EXISTS sku_item";
		mysql_query($sql) or sql_error($sql);			
		$sql = "CREATE TEMPORARY TABLE sku_item (
						`item_id` INT NOT NULL ,
						`qty` INT NOT NULL ,
						`ssku` VARCHAR( 255 ) NOT NULL ,
						`aavail_code` INT NOT NULL ,
						INDEX ( `item_id`),
						INDEX (`qty`),
						INDEX (`ssku` )
						) ENGINE = MYISAM COMMENT = 'temp table for hub_insert_xfer_from_store_SWE'";
		mysql_query($sql) or sql_error($sql);	
		$sql = "INSERT INTO sku_item
						SELECT si.item_id, sf.qty, min( sf.sku ) AS ssku, min( sf.avail_code ) AS aavail_code
						FROM SWE_store_feed sf
						INNER JOIN item_store2 si ON ( sf.sku = si.store_sku
						AND si.store_id =1 )
						GROUP BY si.item_id, sf.qty";
		mysql_query($sql) or sql_error($sql);	
			
		//$sql = "DELETE FROM item_qty";
		$sql = "DROP TEMPORARY TABLE IF EXISTS item_qty";
		mysql_query($sql) or sql_error($sql);			
		$sql = "CREATE TEMPORARY TABLE item_qty (
						`item_id` INT NOT NULL ,
						`nqty` INT NOT NULL ,
						INDEX ( `item_id`),
						INDEX (`nqty`)
						) ENGINE = MYISAM COMMENT = 'temp table for hub_insert_xfer_from_store_SWE'";
		mysql_query($sql) or sql_error($sql);			
		$sql = "INSERT INTO item_qty
						SELECT si2.item_id, max( SWE_store_feed.qty ) AS nqty
						FROM SWE_store_feed
						INNER JOIN item_store2 si2 ON ( SWE_store_feed.sku = si2.store_sku
						AND si2.store_id =1 )
						GROUP BY si2.item_id";
		mysql_query($sql) or sql_error($sql);		

//		$sql = "INSERT INTO xfer_products_SWE ( catalogid, cstock, sku, ccode, cost, avail_code )   
//						SELECT sku_item.item_id AS catalogid, sku_item.qty AS cstock, sku_item.ssku, qs.ItemLookupCode AS ccode, ROUND(qs.Cost,2), sku_item.aavail_code
//						FROM (sku_item INNER JOIN item_qty ON (sku_item.item_id = item_qty.item_id and sku_item.qty = item_qty.nqty)) 
//						INNER JOIN qs2000_item AS qs ON sku_item.ssku = qs.ID";
                // Products with stock from pos only
		$sql = "INSERT INTO xfer_products_SWE ( catalogid, cstock, sku, ccode, cost, avail_code )   
						SELECT sku_item.item_id AS catalogid, sku_item.qty AS cstock, sku_item.ssku, qs.`Custom Field 5` AS ccode, ROUND(qs.`Average Unit Cost`,2), sku_item.aavail_code
						FROM (sku_item INNER JOIN item_qty ON (sku_item.item_id = item_qty.item_id and sku_item.qty = item_qty.nqty)) 
						INNER JOIN pos AS qs ON sku_item.ssku = qs.`Item Number`";
		mysql_query($sql) or sql_error($sql);		
			
	}

//Equivalent:  hub_update_xfer_from_item_SWE
	public static function hub_update_xfer_from_item_SWE() {					
		self::optimize();
		$sql = "UPDATE (xfer_products_SWE AS x INNER JOIN item ON x.catalogid = item.id) 
						LEFT JOIN item_price AS pr ON item.id = pr.item_id and pr.store_id = '1' 
						SET 
                                                x.stocktype = COALESCE(item.drysweet, ''),
						x.mfg = COALESCE(item.Producer,''), 
						x.cvintage = COALESCE(item.Vintage,''),					
						x.cdescription = COALESCE(item.LongDesc,''),
						x.cimageurl = COALESCE(item.cimageurl,''), 
						x.vname = COALESCE(item.varietal,''), 
						x.name = COALESCE(item.name,''), 
						x.country = COALESCE(item.country,''), 
						x.cregion = COALESCE(item.Region,''), 
						x.appellation = COALESCE(item.Appellation,''), 
						x.subappellation = COALESCE(item.`sub-appellation`,''), 
						x.csize = COALESCE(item.Size,''), 
						x.RobertParkerRating = COALESCE(item.`RP Rating`,''), 
						x.RobertParkerReview = COALESCE(item.`RP Review`,''), 
						x.WineSpectatorRating = item.`WS Rating`, 
						x.WineSpectatorReview = COALESCE(item.`WS Review`,''), 
						x.WineEnthusiaisRating = item.`WE Rating`, 
						x.WineEnthusiastReview = item.`WE Review`, 
						x.StephenTanzerRating = item.`ST Rating`, 
						x.StepehnTanzerReview = COALESCE(item.`ST Review`,''), 
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

//		mysql_query($sql) or sql_error($sql);		
//		self::update_twelve_bot_price_SWE();
//		self::update_meta_description();
//		self::update_keywords();		
	}
	
	public static function update_meta_description() {
		$sql = "UPDATE (xfer_products_SWE AS x INNER JOIN item as i ON x.catalogid = i.ID)    
						SET x.meta_description  =  
							CONCAT(
								'Buy ',
								IF(COALESCE(i.Producer, '') <> '', CONCAT(i.Producer, ' '),  ''),
								IF(COALESCE(i.name, '') <> '', CONCAT(i.name, ' '),  ''),
								IF(COALESCE(i.Vintage, '') <> '', CONCAT(i.Vintage, ' '),  ''),	
								IF(COALESCE(i.Size, '') <> '', CONCAT(i.Size, ' '),  ''),
								'from ',
								IF(COALESCE(i.`sub-appellation`, '') <> '', CONCAT(i.`sub-appellation`, ' '),  ''),	
								IF(COALESCE(i.Appellation, '') <> '', CONCAT(i.Appellation, ' '),  ''),		
								IF(COALESCE(i.Region, '') <> '', CONCAT(i.Region, ' '),  ''),		
								IF(COALESCE(i.Country, '') <> '', CONCAT(i.Country, ' '),  ''),	
								'at discount prices on www.saratogwine.com'
							)
						WHERE x.hide = 0";
		mysql_query($sql) or sql_error($sql);	
	}
	
	public static function update_keywords() {	
		$sql = "UPDATE (xfer_products_SWE AS x INNER JOIN item as i ON x.catalogid = i.ID)    
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
									'www.saratogawine.com'		
							)

						WHERE x.hide = 0 AND COALESCE(x.keywords, '') = ''";	
		mysql_query($sql) or sql_error($sql);							
	}
	
	public static function update_twelve_bot_price_SWE() {
		$sql = "UPDATE (xfer_products_SWE AS x INNER JOIN item ON x.catalogid = item.id) 
						LEFT JOIN item_price_twelve_bottle AS pr ON item.id = pr.item_id and pr.store_id = '1' 
						SET 
						x.ctwelvebottleprice  = COALESCE(pr.price,'0'),
						x.ctwelvebottlecost  = COALESCE(pr.cost,'0')";
		mysql_query($sql) or sql_error($sql);	
			
		$sql = "UPDATE xfer_products_SWE 
						SET ctwelvebottleprice = 0
						WHERE cprice <= ctwelvebottleprice
						AND COALESCE(cprice, 0) > 0";
		mysql_query($sql) or sql_error($sql);		

//moved to set_twelvebot_price_to_zero

//		$sql = "UPDATE xfer_products_SWE 
//						SET ctwelvebottleprice = 0
//						WHERE avail_code = '1' 
//						AND cstock < 12";
//		mysql_query($sql) or sql_error($sql);	
		
		//don't need this anymore because custom field 4 is now the manual price for 12bot
		
//		$sql = "UPDATE xfer_products_SWE as x
//						INNER JOIN SWE_store_feed as s
//						ON s.sku = x.sku
//						SET x.ctwelvebottleprice = IF(COALESCE(s.manual_price,0) > 0, 0, x.ctwelvebottleprice)
//						WHERE COALESCE(x.sku, '') <> ''";		
//		mysql_query($sql) or sql_error($sql);		

		//@todo move this
		//seems like some manual prices aren't set right in item_price.class.php
		$sql = "UPDATE xfer_products_SWE as x
						INNER JOIN SWE_store_feed as s
						ON s.sku = x.sku
						SET x.cprice = IF(COALESCE(s.manual_price,0) > 0, s.manual_price, x.cprice)
						WHERE COALESCE(x.sku, '') <> ''";		
		mysql_query($sql) or sql_error($sql);		
	}	
	
	public static function set_twelvebot_cost_to_zero() {
		$sql = "UPDATE xfer_products_SWE 
						SET ctwelvebottlecost = 0
						WHERE cprice <= 0 OR ctwelvebottleprice <= 0";
		mysql_query($sql) or sql_error($sql);				
	}	
	
//Equivalent:  hub_update_xfer_from_feeds_SWE
//this has been re-written as INSERT DUPLICATE KEY UPDATE which is probably what's needed
//needed to update the catalogid so it's unique
	public static function hub_update_xfer_from_feeds_SWE() {
//		$sql = "UPDATE xfer_products_SWE AS x 
//						RIGHT JOIN xfer_temp AS a ON x.catalogid = a.catalogid 
//						SET x.catalogid = a.catalogid, 
//						x.ccode = IF(Trim(COALESCE(x.ccode,'')) = '',a.xref,x.ccode), 
//						x.cost = IF(COALESCE(x.cost,0) = 0,a.cost,x.cost), 
//						x.avail_code = IF(x.avail_code > -1 and x.avail_code < 3,IF(a.cstock > 0,2,1),x.avail_code)";
            // ON DUPLICATE trigers for products which have also stock it feeds
            $sql = "INSERT INTO xfer_products_SWE(catalogid, ccode, cost, avail_code)			
						SELECT a.catalogid,  
							IF(Trim(COALESCE(x.ccode,'')) = '',a.xref,x.ccode), 
							IF(COALESCE(x.cost,0) = 0,a.cost,x.cost), 
							IF(COALESCE(x.avail_code, 0) > -1 and COALESCE(x.avail_code, 0) < 3,IF(a.cstock > 0,2,1),COALESCE(x.avail_code, 0))
						FROM xfer_products_SWE AS x 
						RIGHT JOIN xfer_temp AS a ON x.catalogid = a.catalogid
						ON DUPLICATE KEY UPDATE catalogid = a.catalogid, 
						ccode = IF(Trim(COALESCE(x.ccode,'')) = '',a.xref,x.ccode), 
						cost = IF(COALESCE(x.cost,0) = 0,a.cost,x.cost), 
						avail_code = IF(COALESCE(x.avail_code, 0) > -1 and COALESCE(x.avail_code, 0) < 3,IF(a.cstock > 0,2,1),COALESCE(x.avail_code, 0))";
		mysql_query($sql) or sql_error($sql);				
			
		$sql = "UPDATE xfer_products_SWE AS x
						INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku
						SET 
						x.avail_code = IF(COALESCE(x.avail_code, 0) > -1 and COALESCE(x.avail_code, 0) < 3,IF(pr.stock > 0,2,x.avail_code),COALESCE(x.avail_code, 0))
						 
						WHERE 
						COALESCE(x.sku, '') <> '' 
						AND ( COALESCE(pr.store_sku, '') <> '' AND COALESCE(pr.item_id, 0) <> 0 AND COALESCE(pr.xref, '') <> '' AND COALESCE(pr.price, 0) <> 0 AND COALESCE(pr.supplier_id, 0) <> 0 AND COALESCE(pr.stock, 0) <> 0 AND COALESCE(pr.cost, 0) <> 0)
						";		
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_set_hide_true_SWE
	public static function hub_set_hide_true_SWE() {
		$sql = "UPDATE " . self::table_name() . " SET hide = '1'";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_set_status_for_avail_code_0_SWE
	public static function hub_set_status_for_avail_code_0_SWE() {
//		$sql = "UPDATE " . self::table_name() . " AS s 
//						SET s.hide = '0'
//						WHERE (s.avail_code = '0' and s.cstock > 0)";
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0',
						s.avail_code = '1'
						WHERE (s.avail_code = '0' and s.cstock > 0)";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_set_status_for_avail_code_1_SWE
	public static function hub_set_status_for_avail_code_1_SWE() {
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0', 
						s.minimumquantity = Null
						WHERE (s.avail_code = '1' and s.cstock > 0)";
		mysql_query($sql) or sql_error($sql);				
	}	
//Equivalent:  hub_set_status_for_avail_code_2_SWE
	public static function hub_set_status_for_avail_code_2_SWE() {
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0'
						WHERE s.avail_code = '2'";
		mysql_query($sql) or sql_error($sql);				
	}

//Equivalent:  hub_set_status_for_avail_code_3_SWE
	public static function hub_set_status_for_avail_code_3_SWE() {
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0'
						WHERE s.avail_code = '3'";
		mysql_query($sql) or sql_error($sql);				
	}
		
//Equivalent:  hub_set_status_for_avail_code_4_SWE
	public static function hub_set_status_for_avail_code_4_SWE() {
		$sql = "UPDATE " . self::table_name() . " AS s 
						SET s.hide = '0'
						WHERE s.avail_code = '4'";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_set_hide_true_for_certain_products_SWE
	public static function hub_set_hide_true_for_certain_products_SWE() {
		$sql = "UPDATE " . self::table_name() . " AS x, hub_config AS c 
						SET x.hide = IF((x.avail_code = '2' and c.display_feed_items_SWE = False) or  Instr(COALESCE(c.bottle_size_do_not_display,' '),x.csize)
						or COALESCE(x.mfg,'') = '' or COALESCE(x.name,'') = '', True, x.hide)";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_SWE_hide_no_vintage_if_similar_with_vintage
	public static function hub_SWE_hide_no_vintage_if_similar_with_vintage() {
                $sql = "UPDATE " . self::table_name() . " SET mfg=REPLACE(mfg, '&amp;', '&')";
                mysql_query($sql) or sql_error($sql);

		$sql = "UPDATE " . self::table_name() . " AS x 
						INNER JOIN (SELECT mfg, name, vname, csize 
						from " . self::table_name() . "
						where (Trim(COALESCE(cvintage,'')) <> '' and hide = False))  AS y 
						ON (x.mfg = y.mfg) AND (x.name = y.name) AND (x.vname = y.vname) AND (x.csize = y.csize) 
						SET x.hide = true
						WHERE (isnull(x.cvintage) or Trim(x.cvintage) = '')";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_hide_price_less_than_3_SWE	
	public static function hub_hide_price_less_than_3_SWE() {
		$sql = "UPDATE " . self::table_name() . "
						SET hide = True
						WHERE cprice < 3 or Cost = '0'";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_set_min_qty_price_threshold_SWE
	public static function hub_set_min_qty_price_threshold_SWE() {
		$sql = "UPDATE (select * from hub_price_settings where store_id = '1') AS s, 
						" . self::table_name() . " AS x 
						INNER JOIN item_store2 AS i ON x.catalogid = i.item_id and i.store_id = '1' 
						SET x.minimumquantity = IF( x.cost <= s.SWE_cost_threshold and x.avail_code = '2' and x.cstock <= 0, SWE_min_qty_under_cost_threshold,Null)";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  hub_set_domaine_min_3_SWE	
	public static function hub_set_domaine_min_3_SWE() {
		$sql = "UPDATE (" . self::table_name() . " AS x 
						INNER JOIN item_xref AS i ON i.item_id = x.catalogid) 
						INNER JOIN BevAccessFeeds AS s 
						ON i.xref = s.xref 
						SET minimumquantity = IF(minimumquantity < 3, '3',minimumquantity)
						WHERE (Left(COALESCE(s.companies,' '),6) = 'DOMAIN' and x.cstock <= 3)";	
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_set_bnp_min_6_SWE	
	public static function hub_set_bnp_min_6_SWE() {
		$sql = "UPDATE (" . self::table_name() . " AS x 
						INNER JOIN item_xref AS i ON i.item_id = x.catalogid) 
						INNER JOIN BevAccessFeeds AS s ON i.xref = s.xref 
						SET minimumquantity = IF(minimumquantity < 6, '6',minimumquantity)
						WHERE (Left(COALESCE(s.companies,'xxx'),3) = 'BNP' and x.cstock <= 6)";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_set_polaner_min_3	
	public static function hub_set_polaner_min_3() {
		$sql = "UPDATE " . self::table_name() . " AS p 
						INNER JOIN item_xref AS i 
						ON i.item_id = p.catalogid 
						SET minimumquantity = IF(minimumquantity < 1, '1',minimumquantity)
						WHERE Left(COALESCE(i.xref,'     '),5) = 'POLA-' and p.cstock <= 3";
		mysql_query($sql) or sql_error($sql);						
	}
//Equivalent:  hub_set_angels_min	
	public static function hub_set_angels_min() {
//		$sql = "UPDATE (" . self::table_name() . " AS p 
//						INNER JOIN item AS i ON p.catalogid = i.id) 
//						INNER JOIN item_xref AS si ON si.item_id = p.catalogid 
//						SET p.minimumquantity = IF(si.bot_per_case = 3 and COALESCE(p.cstock, 0) = 0, '3', IF(si.bot_per_case = 6 and COALESCE(p.cstock, 0) = 0, '6', IF(si.bot_per_case = 12 and COALESCE(p.cstock, 0) = 0, '3', p.minimumquantity)))
//						WHERE Left(COALESCE(si.xref,'     '),5) = 'ANGEL'";

		$sql = "UPDATE (" . self::table_name() . " AS p 
						INNER JOIN item AS i ON p.catalogid = i.id) 
						INNER JOIN item_xref AS si ON si.item_id = p.catalogid 
						SET p.minimumquantity = IF(si.bot_per_case = 3 and COALESCE(p.cstock, 0) = 0, '3', IF((si.bot_per_case = 6 OR si.bot_per_case = 24)  and COALESCE(p.cstock, 0) = 0, '6', IF(si.bot_per_case = 12 and COALESCE(p.cstock, 0) = 0, '3', p.minimumquantity)))
						WHERE Left(COALESCE(si.xref,'     '),5) = 'ANGEL'";		
		mysql_query($sql) or sql_error($sql);			
	}
//Equivalent:  hub_set_wildman_min	
	public static function hub_set_wildman_min() {
//		$sql = "UPDATE (" . self::table_name() . " AS p 
//						INNER JOIN item AS i ON p.catalogid = i.id) 
//						INNER JOIN item_xref AS si ON si.item_id = p.catalogid 
//						SET p.minimumquantity = IF(p.cstock > 0, '1', i.bot_per_case)
//						WHERE Left(COALESCE(si.xref,'    '),4) = 'WLDMN'";
    $sql = "UPDATE (xfer_products_SWE AS p INNER JOIN item AS i ON p.catalogid = i.id) 
            INNER JOIN item_xref AS si ON si.item_id = p.catalogid
            SET p.minimumquantity = '0'
            WHERE Left(COALESCE(si.xref,'    '),4) = 'WDMN'";
		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  hub_set_vision_min	
	public static function hub_set_vision_min() {
//		$sql = "UPDATE (" . self::table_name() . " AS p 
//						INNER JOIN item AS i ON p.catalogid = i.id) 
//						INNER JOIN item_xref AS si ON si.item_id = p.catalogid 
//						SET p.minimumquantity = IF(p.cstock > 0, '1', i.bot_per_case)
//						WHERE Left(COALESCE(si.xref,'    '),4) = 'VISN'";
    $sql = "UPDATE (xfer_products_SWE AS p INNER JOIN item AS i ON p.catalogid = i.id) 
            INNER JOIN item_xref AS si ON si.item_id = p.catalogid
            SET p.minimumquantity =  IF(i.bot_per_case < 12  and COALESCE(p.cstock, 0) = 0, IF(COALESCE(i.bot_per_case,0) = 0,'12',i.bot_per_case), IF(si.bot_per_case >= 12 and COALESCE(p.cstock, 0) = 0, '12', IF(COALESCE(p.minimumquantity,0) = 0,'12', p.minimumquantity)))  
            WHERE Left(COALESCE(si.xref,'    '),4) = 'VISN'";
		mysql_query($sql) or sql_error($sql);					
	}
	
//Equivalent:  hub_set_vias_min	
	public static function hub_set_vias_min() {
//		$sql = "UPDATE (xfer_products_SWE AS p 
//						INNER JOIN item AS i ON p.catalogid = i.id) 
//						INNER JOIN item_xref AS si ON si.item_id = p.catalogid 
//						SET p.minimumquantity = i.bot_per_case
//						WHERE Left(COALESCE(si.xref,\"   \"),3) = \"VS-\"";
    $sql = "UPDATE (xfer_products_SWE AS p INNER JOIN item AS i ON p.catalogid = i.id) 
            INNER JOIN item_xref AS si ON si.item_id = p.catalogid
            SET p.minimumquantity = IF(i.bot_per_case < 6  and COALESCE(p.cstock, 0) = 0, i.bot_per_case, IF(si.bot_per_case >= 6 and COALESCE(p.cstock, 0) = 0,
            '6', p.minimumquantity))
            WHERE Left(COALESCE(si.xref,\"   \"),3) = \"VS-\"";
		mysql_query($sql) or sql_error($sql);				
	}

	
//Equivalent:  hub_set_bear_min	
	public static function hub_set_bear_min() {
		$sql = "UPDATE (xfer_products_SWE AS p 
						INNER JOIN item AS i ON p.catalogid = i.id) 
						INNER JOIN item_xref AS si ON si.item_id = p.catalogid 
						SET p.minimumquantity = IF(p.cstock > 0, '1', i.bot_per_case)
						WHERE Left(COALESCE(si.xref,\"    \"),4) = \"BEAR\"";	
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  hub_set_verity_min	
//no min qty for verity
	public static function hub_set_verity_min() {
		$sql = "UPDATE (xfer_products_SWE AS p 
						INNER JOIN item AS i ON p.catalogid = i.id) 
						INNER JOIN item_xref AS si ON si.item_id = p.catalogid 
						SET p.minimumquantity = '0'
						WHERE Left(COALESCE(si.xref,\"    \"),6) = \"VERITY\"";	
		mysql_query($sql) or sql_error($sql);			
	}	

    public static function hub_set_cw_import_min() {
        $sql = "UPDATE (" . self::table_name() . " AS p 
                        INNER JOIN item AS i ON p.catalogid = i.id) 
                        INNER JOIN item_xref AS si ON si.item_id = p.catalogid 
                        SET p.minimumquantity = '0' 
                        WHERE si.xref LIKE 'CW%'";
        mysql_query($sql) or sql_error($sql);
    }

public static function set_supplierid_by_item_xref() {

                $sql = "UPDATE xfer_products_SWE as x
                                                INNER JOIN xfer_temp as xt ON x.catalogid = xt.catalogid 
                                                INNER JOIN item_xref as i ON x.catalogid = i.item_id AND xt.xref=i.xref 
                                                SET x.supplierid = COALESCE(i.supplier_id, x.supplierid)
                                                WHERE i.store_id = 1";
                mysql_query($sql) or sql_error($sql);

                $sql = "UPDATE xfer_products_SWE as x
                                                INNER JOIN item_xref as i ON x.catalogid = i.item_id
                                                SET x.supplierid = COALESCE(i.supplier_id, x.supplierid)
                                                WHERE i.store_id = 1 and COALESCE(x.supplierid, 0) = 0";
                mysql_query($sql) or sql_error($sql);
}

public static function set_supplierid_by_pos() {
                $sql = "UPDATE `xfer_products_SWE` as x 
                                                INNER JOIN pos as p ON p.`Item Number` = x.sku
                                                SET x.supplierid = p.`Vendor Code`
                                                WHERE coalesce(x.sku, '') <> ''
                                                AND x.hide = 0
                                                and COALESCE(x.supplierid, 0) = 0";
                mysql_query($sql) or sql_error($sql);
}

public static function set_supplierid_by_item_price() {
                $sql = "UPDATE xfer_products_SWE AS x
                                                INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku
                                                SET 
                                                x.cprice =  COALESCE(pr.price, x.cprice), 
                                                x.Cost = IF(x.cstock = 0, COALESCE(pr.cost, x.Cost), x.Cost), 
                                                x.ccode = COALESCE(pr.xref, x.ccode),
                                                x.supplierid = COALESCE(pr.supplier_id, x.supplierid)    
                                                WHERE 
                                                COALESCE(x.sku, '') <> '' 
                                                AND ( COALESCE(pr.store_sku, '') <> '' AND COALESCE(pr.item_id, 0) <> 0 AND COALESCE(pr.xref, '') <> '' AND COALESCE(pr.price, 0) <> 0 AND COALESCE(pr.supplier_id, 0) <> 0 AND COALESCE(pr.stock, 0) <> 0 AND COALESCE(pr.cost, 0) <> 0)
                                                AND x.hide = 0";
                mysql_query($sql) or sql_error($sql);
}

	public static function set_supplierid() {
                $sql = "UPDATE xfer_products_SWE as x
                                                INNER JOIN xfer_temp as xt ON x.catalogid = xt.catalogid 
                                                INNER JOIN item_xref as i ON x.catalogid = i.item_id AND xt.xref=i.xref 
                                                SET x.supplierid = COALESCE(i.supplier_id, x.supplierid)
                                                WHERE i.store_id = 1";
                mysql_query($sql) or sql_error($sql);

                $sql = "UPDATE xfer_products_SWE as x
                                                INNER JOIN item_xref as i ON x.catalogid = i.item_id
                                                SET x.supplierid = COALESCE(i.supplier_id, x.supplierid)
                                                WHERE i.store_id = 1 and COALESCE(x.supplierid, 0) = 0";
                mysql_query($sql) or sql_error($sql);

		$sql = "UPDATE `xfer_products_SWE` as x 
						INNER JOIN pos as p ON p.`Item Number` = x.sku
						SET x.supplierid = p.`Vendor Code`
						WHERE coalesce(x.sku, '') <> ''
						AND x.hide = 0
						and COALESCE(x.supplierid, 0) = 0";
		mysql_query($sql) or sql_error($sql);		

		
		$sql = "UPDATE xfer_products_SWE AS x
						INNER JOIN item_price AS pr ON x.catalogid = pr.item_id and pr.store_id = '1' and x.sku = pr.store_sku
						SET 
						x.cprice =  COALESCE(pr.price, x.cprice), 
						x.Cost = IF(x.cstock = 0, COALESCE(pr.cost, x.Cost), x.Cost), 
						x.ccode = COALESCE(pr.xref, x.ccode),
						x.supplierid = COALESCE(pr.supplier_id, x.supplierid)	 
						WHERE 
						COALESCE(x.sku, '') <> '' 
						AND ( COALESCE(pr.store_sku, '') <> '' AND COALESCE(pr.item_id, 0) <> 0 AND COALESCE(pr.xref, '') <> '' AND COALESCE(pr.price, 0) <> 0 AND COALESCE(pr.supplier_id, 0) <> 0 AND COALESCE(pr.stock, 0) <> 0 AND COALESCE(pr.cost, 0) <> 0)
						AND x.hide = 0";
		mysql_query($sql) or sql_error($sql);			
//
//						AND (							 
//							CAST(x.cprice as decimal(19,4)) <> p.`MSRP` 
//							OR x.supplierid <> p.`Vendor Code` 
//							OR s.SupplierName <> p.`Vendor Name`						
//						)";		
	}
	
	public static function update_images() {

		$sql = "UPDATE xfer_products_SWE as xfer
						INNER JOIN item_xref as ix
						ON xfer.catalogid = ix.item_id
						INNER JOIN bevaccess_supplement as b
						ON b.xref = ix.xref
						SET 
						xfer.cimageurl = IF(TRIM(b.img_bot) <> '', TRIM(b.img_bot), IF(TRIM(b.img_lab) <> '', TRIM(b.img_lab), xfer.cimageurl)),
						xfer.extendedimage = IF(TRIM(b.img_bot) <> '', TRIM(b.img_bot), IF(TRIM(b.img_lab) <> '', TRIM(b.img_lab), xfer.extendedimage))
						WHERE xfer.hide = 0
						AND xfer.cimageurl LIKE '%no_image.jpg%'";
		mysql_query($sql) or sql_error($sql);				

	}
	
	
	public static function in_stock_sale_swe() {
		$sql = "SELECT in_stock_sale FROM hub_price_settings WHERE store_id = '1'";
		$result = mysql_query($sql) or sql_error($sql);			
		$row = mysql_fetch_assoc($result);
		if($row['in_stock_sale'] > 0) {
			$in_stock_sale = 1 + $row['in_stock_sale']; 
			$sql = "SELECT use_12bot_price_with_in_stock_sale FROM hub_price_settings WHERE store_id = '1'";
			$res = mysql_query($sql) or sql_error($sql);			
			$row = mysql_fetch_assoc($res);	
			$twelve_price_sql = '';
			$twelve_bot_message = '';
			
			if($row['use_12bot_price_with_in_stock_sale']) {				
				$twelve_bot_message = ' and there is 12 bot pricing for in stock items';				
			}
			else {
				$twelve_price_sql = 'x.ctwelvebottleprice = 0,';
				$twelve_bot_message = ' and there is no 12 bot pricing for in stock items';				
			}
			
			$sql = "UPDATE xfer_products_SWE as x
							LEFT JOIN pos as p
							ON x.catalogid = p.`Alternate Lookup` and x.sku = p.`Item Number`
							SET 
							$twelve_price_sql
							x.avail_code = 1,							
							x.cprice = ROUND(x.Cost * $in_stock_sale, 2)
							WHERE x.hide = 0
							AND COALESCE(x.cstock,0 ) > 0
							AND COALESCE(p.`Custom Price 3`, 0) = 0";
			mysql_query($sql) or sql_error($sql);		

			
			$sql = "UPDATE xfer_products_SWE as x
							LEFT JOIN pos as p
							ON x.catalogid = p.`Alternate Lookup` and x.sku = p.`Item Number`
							SET 
							$twelve_price_sql
							x.avail_code = 1							
							WHERE x.hide = 0
							AND COALESCE(x.cstock,0 ) > 0";
			mysql_query($sql) or sql_error($sql);
			if($row['use_12bot_price_with_in_stock_sale']) {				
				$sql = "UPDATE xfer_products_SWE 
								SET ctwelvebottleprice = 0
								WHERE cprice <= ctwelvebottleprice
								AND COALESCE(cprice, 0) > 0";
				mysql_query($sql) or sql_error($sql);				
			}				
		
			echo __CLASS__  . ' ' . __FUNCTION__ . " complete at $in_stock_sale $twelve_bot_message<br />";				
		}	
	}

	/**
	 * 
	 * @todo add other conditions to set ctwelvebottleprice to 0
	 */
	public static function set_twelvebot_price_to_zero() {
		$sql = "UPDATE xfer_products_SWE 
						SET ctwelvebottleprice = 0
						WHERE avail_code = '1' 
						AND cstock < 12";
		mysql_query($sql) or sql_error($sql);			
	}	
}//end class
