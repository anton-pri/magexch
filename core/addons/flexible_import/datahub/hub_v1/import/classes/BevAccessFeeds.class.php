<?php

/**
 * this has table_id
  ALTER TABLE `BevAccessFeeds` ADD `table_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
  ALTER TABLE  `BevAccessFeeds` ADD INDEX (  `skus` );
  ALTER TABLE `BevAccessFeeds` ADD INDEX ( `prod_item` );
 ALTER TABLE `BevAccessFeeds` ADD INDEX ( `xref` );
 ALTER TABLE `BevAccessFeeds` ADD INDEX ( `size` ); 
 ALTER TABLE `BevAccessFeeds` ADD INDEX ( `vintage` );  
 ALTER TABLE `BevAccessFeeds` ADD INDEX ( `botpercase` );  
 * above sql is should not run on create due to population problem
 * table must be populated from bullzip first 
 * 

 */

/**
 * @todo UP_vpr integration see google docs for instructions
 * done but needs to verified, probably with Matt
 *
 */
class BevAccessFeeds extends feed {
	public static $table = 'BevAccessFeeds';															
	public static $feed_file = '';
	
	
//Equivalent:  BevA_insert_POS		
	public static function insert_pos() {
                global $config; 
		pos::binlocation_to_id();		
		$sql = "INSERT IGNORE INTO pos ( 
						`Item Number`,
						`Item Name`,
						`Item Description`,						
						`Attribute`,
						`Size`,
						`Average Unit Cost`,
						`Regular Price`,
						`Department Name`,
                                                `Custom Field 1`,
						`Vendor Name`,
						`Manufacturer`,
						`Custom Field 2`,
						`Custom Field 3`,
						`Custom Field 5`					
						)
						
						SELECT DISTINCT 		
						'' AS Expr1,		
						Replace(CONCAT(Left(Trim(c.Producer),10) , TRIM(CONCAT(Left(CONCAT(c.name , \" \"),14) , Right(CONCAT(\" \" , c.Vintage),2) , \".\" , Left(CONCAT(c.Size , \" \"),3)))),\" \",\"\") AS Expr2,
						CONCAT(c.Producer , \" \" , IF(c.name <> c.varietal,CONCAT(c.name , \" \"),\"\") , c.varietal , \" \" , c.Vintage , \" \" , c.Size , \" \" , c.Country , \" \" , c.Region , \" \" , c.appellation , \" \" , c.`sub-appellation`) AS  Expr3, 					
						c.Vintage AS Expr4,
						c.size,
						Round(IF(IsNull(`f`.`case_price`) or f.case_price = 0 or f.case_price <= f.bot_price,`f`.`bot_price`,`f`.`case_price`/`f`.`botpercase`),2) AS Expr5, 
						Round(IF(IsNull(`f`.`case_price`) or f.case_price = 0 or f.case_price <= f.bot_price,`f`.`bot_price`,`f`.`case_price`/`f`.`botpercase`)*1.5,0)-0.01  AS Expr6, 						
                                                IF((INSTR('".$config['flexible_import']['fi_spirit_varietals']."',c.varietal) AND c.varietal!=''),'Spirit','Wine'),
						c.country,
						sup.SupplierName,
						c.Producer,
						c.varietal,
						c.bottles_per_case,
						c.xref
						
						FROM (((BevAccessFeeds AS f INNER JOIN feeds_item_compare AS c ON f.xref = c.xref) 
						LEFT JOIN BevA_Typetbl ON f.univ_cat = BevA_Typetbl.type_code) 
						LEFT JOIN BevA_company_supplierID_map AS m ON Trim(Left(f.companies,InStr(CONCAT(f.companies , \" \"),\" \"))) = m.company) 
						LEFT JOIN pos AS qi ON COALESCE(c.catalogid,0)  = qi.`Alternate Lookup`
						LEFT JOIN Supplier as sup ON c.supplier_id = sup.supplier_id
						WHERE (c.store_id = 1 and c.Source = 'Feed_BEVA'  and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";

		mysql_query($sql) or sql_error($sql);
		pos::binlocation_to_varchar();			
	}
	
	public static function table_name() {
		return self::$table;
	}	
	
	/**
	 * @todo need to add new supplier here as well?
	 * probably not, this really a bev thing
	 *
	 */	
	public static function update_supplier_id() {
		$sql = "update feeds_item_compare 
							set supplier_id = if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'COLONY', '88', 
								if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'LAUBER', '11',
									if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'MSCOTT', '24',
										if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'OPICI', '83',
											if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'SWS', '63',
												if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'WINBOW', '13',
													if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'BNP', '19',								
															if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'BOWL', '150',
																if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'SKUR' OR SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'SKURN', '103',
																	if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'TOUTON', '27',
																		if(SUBSTRING_INDEX( `wholesaler` , ' ', 1 ) = 'DOMAIN', '31',
																			supplier_id
																		)												
																	)									
																)									
															)								
														)
												)
											)
										)
									)
								)
							)	
							WHERE source = 'Feed_BEVA';";
		mysql_query($sql) or sql_error($sql);
		
	}
	
	public static function update_supplier_id_item_xref() {
		$sql = "UPDATE item_xref as x 
						INNER JOIN BevAccessFeeds as b
						ON x.xref = b.xref 
						set x.supplier_id = if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'COLONY', '88', 
							if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'LAUBER', '11',
								if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'MSCOTT', '24',
									if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'OPICI', '83',
										if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'SWS', '63',
											if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'WINBOW', '13',
													if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'BNP', '19',	
														if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'BOWL', '150',
															if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'SKUR' OR SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'SKURN', '103',
																if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'TOUTON', '27',
																	if(SUBSTRING_INDEX( b.companies , ' ', 1 ) = 'DOMAIN', '31',
																		supplier_id
																	)					
																)		
															)		
														)	
													)
												)					
											
										)
									)
								)
							)
						)";		
		mysql_query($sql) or sql_error($sql);					
	}	
	
//Equivalent:  BevA_insert_compare_union_feeds_item	
	public static function insert_compare_union_feeds_item() {
		$query = BevAccessFeeds::BevAFeedsMunge();
		$sql = "INSERT INTO feeds_item_compare ( Source, wholesaler, producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, description, store_id )
						SELECT `a`.`Source` AS Source, `a`.`wholesaler` AS wholesaler, ucwords(`a`.`producer`) AS producer, ucwords(`a`.`Wine`) AS Wine, ucwords(`a`.`Name`) AS Name, `a`.`Vintage` AS Vintage, `a`.`Size` AS `Size`, `a`.`xref` AS xref, `a`.`bottles_per_case` AS bottles_per_case, `a`.`catalogid` AS catalogid, ucwords(`a`.`Region`) AS Region, ucwords(`a`.`country`) AS country, `a`.`varietal` AS varietal, ucwords(`a`.`Appellation`) AS Appellation, ucwords(`a`.`sub-appellation`) AS `sub-appellation`, `a`.`cost` AS cost, `a`.`Parker_rating` AS Parker_rating, `a`.`Parker_review` AS Parker_review, `a`.`Spectator_rating` AS Spectator_rating, `a`.`Spectator_review` AS Spectator_review, `a`.`Tanzer_rating` AS Tanzer_rating, `a`.`Tanzer_review` AS Tanzer_review, `a`.`description`, '1'
						FROM 
						($query) AS a";
		mysql_query($sql) or sql_error($sql);			
		$sql = "DELETE FROM feeds_item_compare
						WHERE Source = 'Feed_BEVA'
						AND (COALESCE(Wine, '') = '' AND COALESCE(Producer, '') = '' AND COALESCE(Producer, '') = '')";
		mysql_query($sql) or sql_error($sql);					
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';						
	}
//Equivalent:  BevAFeedsMunge
	public static function BevAFeedsMunge() {
		$sql = "SELECT 
						'Feed_BEVA' as `Source`, 
						u.`current` as `current`, 
						u.`confstock` as `confstock`, 
						u.`companies` as `wholesaler`, 
						u.`producer` as `producer`, 
						u.`bdesc` AS `Wine`, 
						
						Left(Trim(Replace(Replace(Replace(Replace(If(InStr(u.`bdesc`,\"ml\"),Left(u.`bdesc`,
						
						IF(INSTR(trim(u.`bdesc`), \" \") > 0,
						length( trim( 
						SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, \"ml\"))
						) ) - length( right( trim( 
						SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, \"ml\"))
						) , instr( reverse( trim( 
						SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, \"ml\"))
						 ) ) , \" \" ) -1 ) )
						, 0)
						
						),u.`bdesc`),Coalesce(u.`Vintage`,\"~\"),\"\"),Coalesce(u.`Producer`,\"~\"),\"\"),If(IsNull(u.`Producer`),\"~\",Right(u.`Producer`,LENGTH(u.`Producer`)-
						
						
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
							length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , \" \" ) -1 ) )
						, 0)
						
						
						
						)),\"\"),If(IsNull(u.`Producer`) or not Instr(u.`Producer`,\" \"),\"~\",Mid(
						
						
						
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
						
						length( trim( 
						SUBSTRING(u.`Producer`,1, 
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
							length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , \" \" ) -1 ) )
						, 0)
						
						)
						) ) - length( right( trim( 
						SUBSTRING(u.`Producer`,1, 
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
							length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , \" \" ) -1 ) )
						, 0)
						
						)
						) , instr( reverse( trim( 
						SUBSTRING(u.`Producer`,1, 
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
							length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , \" \" ) -1 ) )
						, 0)
						)
						 ) ) , \" \" ) -1 ) )	
						
						, -1)
						
						
						
						
						+1,
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
							length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , \" \" ) -1 ) )
						, 0)
						-1-
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
						
						length( trim( 
						SUBSTRING(u.`Producer`,1, 
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
							length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , \" \" ) -1 ) )
						, 0)
						
						)
						) ) - length( right( trim( 
						SUBSTRING(u.`Producer`,1, 
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
							length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , \" \" ) -1 ) )
						, 0)
						
						)
						) , instr( reverse( trim( 
						SUBSTRING(u.`Producer`,1, 
						IF(INSTR(trim( u.`Producer` ), \" \") > 0,
							length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , \" \" ) -1 ) )
						, 0)
						)
						 ) ) , \" \" ) -1 ) )	
						
						, -1)
						+1)
						),\"\")),255) as `Name`,
						
						u.`vintage` AS `Vintage`, 
						If(IsNull(u.`size`) Or u.`size`='0',\"\",If(u.`size`='1500',\"1.5Ltr\",If(u.`size`='3000',\"3.0Ltr\",If(u.`size` <1000,
						CONCAT(CAST(u.`size` as CHAR) , \"ml\"),
						If(u.`size`>=1000,CONCAT(CAST(Round(CAST(u.`size` as DECIMAL(10,2))/1000,3) as CHAR) , \"Ltr\"),\"Other\"))))) as `Size`, 
						u.`xref` as `xref`, 
						u.`botpercase` AS `bottles_per_case`,
						null as `catalogid`,
						`r`.`region_2` AS `Region`, 
						`r`.`region_1` AS `country`, 
						ucwords(u.`grape`) AS `varietal`, 
						`r`.`region_3` AS `Appellation`, 
						`r`.`region_4` AS `sub-appellation`, 
						Round(If(IsNull(u.`case_price`) or u.`case_price` = '0',u.`bot_price`,u.`case_price`/u.`botpercase`),2) AS `cost`, 
						null AS `Parker_rating`, 
						null AS `Parker_review`,
						null AS `Spectator_rating`, 
						null AS `Spectator_review`, 
						null AS `Tanzer_rating`, 
						null AS `Tanzer_review`,
						null as `description`
						FROM (BevAccessFeeds u LEFT JOIN item_xref i  on u.`xref` = `i`.`xref`) LEFT JOIN BevA_Reg_text r on u.`reg_text` = `r`.`reg_text`
						WHERE (u.`descriptio` not like '%Combo%' and u.`descriptio` not like '%PK%' and ((current = \"Y\" and confstock = \"Y\") 
						or InStr((select Coalesce(BevA_wholesalers_always_on,\" \") from hub_config),
						
						Trim(Left(CONCAT(Coalesce(u.`companies`,\"xxx\") , \" \"),Instr(CONCAT(Coalesce(u.`companies`,\"xxx\") , \" \"),\" \")))) > 0) 
						
						and isNull(`i`.`xref`)) and LENGTH(Trim(Coalesce(u.`companies`,\" \"))) > 0
						UNION ALL SELECT 
						'Hub' as `Source`, 
						null as `current`, 
						null as `confstock`, 
						null as `wholesaler`, 
						h.`producer` as `producer`, 
						null as `Wine`, 
						h.`name` as `Name`, 
						h.`vintage` as `vintage`, 
						Coalesce(h.`Size`,\"\") as `Size`, 
						null as `xref`, 
						h.`bot_per_case` as `bottles_per_case`,
						`ID` as `catalogid`, 
						h.`Region` as `Region`, 
						h.`country` as `country`, 
						h.`varietal` as `varietal`, 
						h.`Appellation` as `Appellation`, 
						h.`sub-appellation` as `sub-appellation`, 
						null as `cost`, 
						h.`RP Rating` as `Parker_rating`, 
						h.`RP Review` as `Parker_review`, 
						h.`WS Rating` as `Spectator_rating`, 
						h.`WS Review` as `Spectator_review`, 
						h.`ST Rating` as `Tanzer_rating`, 
						h.`ST Review` as `Tanzer_review`, 
						`LongDesc` as `description`
						FROM item h where Coalesce(h.dup_catid,'0') = '0'";
		return $sql;		
	}
	
//Equivalent:  BevA_delete_BevAccessFeeds
	public static function delete_BevAccessFeeds() {
		$sql = "DELETE FROM " .
						self::table_name();
		mysql_query($sql) or sql_error($sql);							
	}
	
//Equivalent:  bevA_update_monthly
	public static function update_monthly() {
		$sql = "UPDATE BevAccessFeeds AS f INNER JOIN UP_VPR AS v ON f.xref = v.xref SET f.xref = v.xref,
						f.bdesc = `v`.`bdesc`,
						f.descriptio = `v`.`descriptio`,
						f.`size` = `v`.`size`,
						f.vintage = `v`.`vintage`,
						f.univ_cat = `v`.`univ_cat`,
						f.lwbn = `v`.`lwbn`,
						f.apc = `v`.`apc`,
						f.bestbot = `v`.`bestbot`,
						f.`date` = `v`.`date`,
						f.botpercase = `v`.`botpercase`,
						f.secpack = `v`.`secpack`,
						f.wholesaler = `v`.`wholesaler`,
						f.prod_item = `v`.`prod_item`,
						f.upc = `v`.`upc`,
						f.case_price = `v`.`case_price`,
						f.bot_price = `v`.`bot_price`,
						f.front_nyc = `v`.`front_nyc`,
						f.postoff = `v`.`postoff`,
						f.spec_price = `v`.`spec_price`,
						f.ripcode = `v`.`ripcode`,
						f.qty1 = `v`.`qty1`,
						f.d_type1 = `v`.`d_type1`,
						f.discount1 = `v`.`discount1`,
						f.qty2 = `v`.`qty2`,
						f.d_type2 = `v`.`d_type2`,
						f.discount2 = `v`.`discount2`,
						f.qty3 = `v`.`qty3`,
						f.d_type3 = `v`.`d_type3`,
						f.discount3 = `v`.`discount3`,
						f.qty4 = `v`.`qty4`,
						f.d_type4 = `v`.`d_type4`,
						f.discount4 = `v`.`discount4`,
						f.qty5 = `v`.`qty5`,
						f.d_type5 = `v`.`d_type5`,
						f.discount5 = `v`.`discount5`,
						f.qty6 = `v`.`qty6`,
						f.d_type6 = `v`.`d_type6`,
						f.discount6 = `v`.`discount6`,
						f.qty7 = `v`.`qty7`,
						f.d_type7 = `v`.`d_type7`,
						f.discount7 = `v`.`discount7`,
						f.qty8 = `v`.`qty8`,
						f.d_type8 = `v`.`d_type8`,
						f.discount8 = `v`.`discount8`,
						f.qty9 = `v`.`qty9`,
						f.d_type9 = `v`.`d_type9`,
						f.discount9 = `v`.`discount9`,
						f.div1 = `v`.`div1`,
						f.div2 = `v`.`div2`,
						f.div3 = `v`.`div3`,
						f.div4 = `v`.`div4`,
						f.div5 = `v`.`div5`,
						f.div6 = `v`.`div6`,
						f.div7 = `v`.`div7`,
						f.div8 = `v`.`div8`,
						f.div9 = `v`.`div9`,
						f.div10 = `v`.`div10`,
						f.div11 = `v`.`div11`,
						f.div12 = `v`.`div12`,
						f.asst_size = `v`.`asst_size`,
						f.organic = `v`.`organic`,
						f.kosher = `v`.`kosher`,
						f.sparkling = `v`.`sparkling`,
						f.productid = `v`.`productid`,
						f.deposit = `v`.`deposit`,
						f.cale_shelf = `v`.`cale_shelf`,
						f.truevint = `v`.`truevint`,
						f.prod_id = `v`.`prod_id`,
						f.whole_desc = `v`.`whole_desc`,
						f.producer = `v`.`producer`,
						f.companies = v.wholesaler";
		mysql_query($sql) or sql_error($sql);			
	}
	
//Equivalent:  bevA_update_monthly_insert_new_xref
	public static function update_monthly_insert_new_xref() {
		$sql = "INSERT INTO " . self::table_name() . " (xref)
						SELECT DISTINCT v.xref AS xref
						FROM UP_VPR AS v LEFT JOIN " . self::table_name() . " AS f ON v.xref = f.xref
						WHERE isnull(f.xref) AND Instr((select BevA_do_not_import from hub_config),v.wholesaler) = 0";
		mysql_query($sql) or sql_error($sql);	
	}
	

	

	//Equivalent: BevA_set_current_null
	public static function set_current_null() {	
		$sql = 'UPDATE ' . BevAccessFeeds::table_name() . '
						SET current = "", confstock = ""';
		mysql_query($sql) or sql_error($sql);				
	}


	//Equivalent: bevA_update_daily
	public static function update_daily() {
		$sql = 'UPDATE ' . BevAccessFeeds::table_name() . ' AS f INNER JOIN UP_prod AS p ON f.xref = p.xref 
					SET f.vintage = `p`.`vintage`,
					 f.prod_id = `p`.`prod_id`,
					 f.companies = `p`.`companies`,
					 f.status = `p`.`status`,
					 f.bdesc = `p`.`bdesc`,
					 f.botpercase = `p`.`botpercase`,
					 f.descriptio = `p`.`descriptio`,
					 f.univ_cat = `p`.`univ_cat`,
					 f.reg_id = `p`.`reg_id`,
					 f.truevint = `p`.`truevint`,
					 f.use_vint = `p`.`use_vint`,
					 f.grape = `p`.`grape`,
					 f.kosher = `p`.`kosher`,
					 f.organic = `p`.`organic`,
					 f.prod_type = `p`.`prod_type`,
					 f.importer = `p`.`importer`,
					 f.cat_id = `p`.`cat_id`,
					 f.type_id = `p`.`type_id`,
					 f.rev = `p`.`rev`,
					 f.des = `p`.`des`,
					 f.wmn = `p`.`wmn`,
					 f.rat = `p`.`rat`,
					 f.fpr = `p`.`fpr`,
					 f.tek = `p`.`tek`,
					 f.rec = `p`.`rec`,
					 f.txt = `p`.`txt`,
					 f.tas = `p`.`tas`,
					 f.lab = `p`.`lab`,
					 f.bot = `p`.`bot`,
					 f.pho = `p`.`pho`,
					 f.log = `p`.`log`,
					 f.oth = `p`.`oth`,
					 f.lwbn = `p`.`lwbn`,
					 f.producer = `p`.`producer`,
					 f.cat_type = `p`.`cat_type`,
					 f.reg_text = `p`.`reg_text`,
					 f.sparkling = `p`.`sparkling`,
					 f.rp = `p`.`rp`,
					 f.we = `p`.`we`,
					 f.ws = `p`.`ws`,
					 f.`current` = `p`.`current`,
					 f.fortified = `p`.`fortified`,
					 f.dessert = `p`.`dessert`,
					 f.closure = `p`.`closure`,
					 f.pack = `p`.`pack`,
					 f.packaging = `p`.`packaging`,
					 f.packtype = `p`.`packtype`,
					 f.skus = `p`.`skus`,
					 f.syn = `p`.`syn`,
					 f.tstamp = `p`.`tstamp`,
					 f.confstock = `p`.`confstock`,
					 f.whvint = `p`.`whvint`,
					 f.univ_prod = `p`.`univ_prod`;';
		mysql_query($sql) or sql_error($sql);				

	}	

	
	//Equivalent: BevA_set_qty_0
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE CAST(Left(coalesce(xref,'xxx'),3) as SIGNED) > 0";
		mysql_query($sql) or sql_error($sql);		
	}
	//Equivalent: BevA_update_item_xref
	public static function update_item_xref() {
		$sql = 'UPDATE ((hub_config AS c INNER JOIN item_xref AS i ON (c.store_id = i.store_id and i.store_id = 1)) INNER JOIN BevAccessFeeds AS f ON i.xref=f.xref) LEFT JOIN item AS i2 ON i.item_id = i2.ID SET i.qty_avail = If(
					  ((f.current = "Y" and f.confstock = "Y") 
					  or InStr(
					                 coalesce(c.BevA_wholesalers_always_on," "),
					                 Trim(
					                         Left(
					                               CONCAT(coalesce(f.companies,"xxx"), " "),
					                               Instr(CONCAT(coalesce(f.companies,"xxx") , " ")," ")					                               
					                          )
					                 )
					         ) > 0)
					
					and not Instr(coalesce(c.BevA_wholesalers_exclude," "), Trim(
					                         Left(
					                               CONCAT(coalesce(f.companies,"xxx"), " "),
					                               Instr(CONCAT(coalesce(f.companies,"xxx") , " ")," ")					                               
					                          )
					                 )),9999,0), i.min_price = 0, i.bot_per_case = CAST(coalesce(f.`botpercase`,"12") as SIGNED), i.cost_per_bottle = Round(f.bot_price,2), i.cost_per_case = Round(If(coalesce(f.case_price,"0") = "0", f.bot_price*coalesce(i2.bot_per_case,
					CAST(coalesce(f.`botpercase`,"12") as SIGNED)),f.case_price),2)';
		mysql_query($sql) or sql_error($sql);		
	}
		
	//Equivalent: hub_apply_splitcase_to_cost
	public static function hub_apply_splitcase_to_cost() {
		$sql = 'UPDATE (item_xref AS i 
						INNER JOIN BevAccessFeeds AS f 
						ON i.xref = f.xref) 
						LEFT JOIN splitcase_charges AS c 
						ON Trim(Left(coalesce(CONCAT(f.companies , " "),"x "),
						InStr(coalesce(CONCAT(f.companies , " "),"x ")," ")))=c.company 
						SET split_case_charge = coalesce(c.charge*If(coalesce(i.bot_per_case,12) > 1 
						AND InStr(c.company,"COLONY"),12/coalesce(i.bot_per_case,12),
						If(coalesce(i.bot_per_case,12) = 1,0,1)),0.75)';
		mysql_query($sql) or sql_error($sql);				
	}
	
	public static function block_wholesaler($wholesalers = array())	{
		if(count($wholesalers) == 0 || !is_array($wholesalers)) return;
		foreach ($wholesalers as $v) {
			$sql = "DELETE FROM " . self::table_name() . "
							WHERE companies LIKE '%$v%'";
 			mysql_query($sql)or sql_error($sql);			
		}
	}		
	

	//Equivalent: BevA_Monthly_update (macro)
	public static function monthly_update() {
		if(UP_VPR::feed_file(FEED_FILE . UP_VPR::get_feed_file(), UP_VPR::table_name())) {
		/**
		 * @todo the 2 comments below
		 */
			//delete up_vpr table 
			UP_VPR::delete_table();
			//import ny file
			UP_VPR::transfer_text(UP_VPR::get_feed_file(), UP_VPR::table_name(), ',');			
			UP_VPR::transfer_text('NY_VPR.CSV', UP_VPR::table_name(), ',');			
			UP_VPR::x();
			UP_VPR::bevA_UP_VPR_set_xref();
			UP_VPR::block_wholesaler(array('opici'));
			BevAccessFeeds::delete_BevAccessFeeds(); 
			BevAccessFeeds::update_monthly_insert_new_xref();
			BevAccessFeeds::update_monthly();
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';					
		}
			
	}
	
//Equivalent:  BevA_daily_import_and_update (macro)
	public static function daily_import_and_update() {
		UP_prod::delete_UP_prod();
		UP_prod::transfer_text();	
		/**
		 * @todo give this a better function name
		 */
		UP_prod::x();
		UP_prod::block_wholesaler(array('opici'));			
		self::block_wholesaler(array('opici'));
		
		UP_prod::block_wholesaler(array('WILDMN'));			
		self::block_wholesaler(array('WILDMN'));

		BevAccessFeeds::daily_update_only();
		//wildman_feed::import_only();
		//BevAccessFeeds::daily_update_only();//rerun bevA_daily_update_only to populate the feed build table for wildman		
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	
	
	//Equivalent:  BevA_Daily_update_only (macro) 
	public static function daily_update_only() {
		UP_prod::prod_set_xref();//Equivalent:  bevA_UP_prod_set_xref
		BevAccessFeeds::set_current_null();
		//UP_prod::update_up_prod_wildman();
		BevAccessFeeds::update_daily();
//		Domaine_feed::update_status();
		BevAccessFeeds::set_qty_0();
		BevAccessFeeds::update_item_xref();
		BevAccessFeeds::hub_apply_splitcase_to_cost();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';	
	}	

	

}//end class

//bdesc ->remove producer from it

