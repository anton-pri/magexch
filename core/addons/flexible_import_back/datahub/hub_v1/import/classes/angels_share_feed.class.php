<?php
/**
 * this has table_id
 * this can be run on create
 
 * 
 * ALTER TABLE `angels_share_feed` ADD PRIMARY KEY ( `ID` );
 */

class angels_share_feed extends feed {
	public static $table = 'angel_share_feed';
	public static $feed_file = '';	
	public static $supplier_id = '69';	
	
	public static function get_supplier_id() {
		return self::$supplier_id;
	}	

	public static function get_supplier_name() {
		$sql = "SELECT * FROM Supplier WHERE supplier_id = '" . self::get_supplier_id() . "'";
		$result = mysql_query($sql) or sql_error($sql);		
		$row = mysql_fetch_array($result);
		return $row['SupplierName'];
	}		
	
//Equivalent:  Angel_share_insert_POS
	public static function insert_pos() {
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
						`Vendor Name`,
						`Custom Field 1`,
						`Custom Field 2`,
						`Custom Field 3`,
						`Custom Field 5`	
						)
						SELECT DISTINCT
						'' AS Expr1,
						Replace(CONCAT(Left(Trim(c.Producer),10) , TRIM(CONCAT(Left(CONCAT(c.name , \" \"),14) , Right(CONCAT(\" \" , c.Vintage),2) , \".\" , Left(CONCAT(c.Size , \" \"),3)))),\" \",\"\") AS Expr2, 
						'' AS Expr3,					
						c.Vintage AS Expr4,
						c.size,
						Round(c.cost,2) AS Expr5,
						Round(CAST(c.cost as DECIMAL(19,4))*1.5,0)-0.01,
						c.country,
						'" . mysql_real_escape_string(self::get_supplier_name()) . "',
						c.Producer,
						c.varietal,
						c.bottles_per_case,
						c.xref
						FROM feeds_item_compare AS c LEFT JOIN pos AS qi ON COALESCE(c.catalogid,0) = qi.`Alternate Lookup`
						WHERE (c.store_id = 1 and Left(c.xref,5) = 'ANGEL' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}		
	
	public static function table_name() {
		return self::$table;
	}
	
	
	public static function set_qty_0() {//Equivalent:  Angel_set_qty_0
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE LEFT(coalesce(i.xref,'      '),6) = 'ANGEL-'";

		mysql_query($sql) or sql_error($sql);	
	}
	
	public static function update_item_xref() {//Equivalent:  Angels_update_item_xref

//Mar 8, 2011, changed CONCAT('ANGEL-',Cast(f.ID as CHAR)) to CONCAT('ANGEL-', f.ID)
//		$sql = "UPDATE (item_xref AS i 
//						INNER JOIN angels_share_feed AS f ON i.xref=CONCAT('ANGEL-', f.ID)) 
//						LEFT JOIN item AS i2 ON i.item_id = i2.ID 
//						SET i.qty_avail = If(Trim(coalesce(f.`Qty Available`,'')) <> 'SOLD OUT',9999,0), 
//						i.min_price = Cast(coalesce(f.`Minimum Price`,'0') as DECIMAL(10,2)), 
//						i.bot_per_case = Cast(coalesce(f.`Qty per Case`,'12') as SIGNED), 
//						i.cost_per_case = Round(Cast(Mid(f.Price,2,InStr(CONCAT(f.Price,' '),' ')-1) as DECIMAL(10,2)),2), 
//						i.cost_per_bottle = Round(Cast(Mid(f.Price,2,InStr(CONCAT(f.Price,' '),' ')-1) as DECIMAL(10,2)),2)	/ Cast(IF(COALESCE(i2.bot_per_case, 0) > 0, i2.bot_per_case, coalesce(f.`Qty per Case`,'12'))  as SIGNED);";
//May 16, 2012 Get rid of left join with item table
			$sql = "UPDATE (item_xref AS i 
							INNER JOIN angels_share_feed AS f ON i.xref=CONCAT('ANGEL-', f.ID)) 					
							SET i.qty_avail = If(Trim(COALESCE(f.`Qty Available`,'')) <> 'SOLD OUT',9999,0), 
							i.min_price = Cast(COALESCE(f.`Minimum Price`,'0') as DECIMAL(10,2)), 
							i.bot_per_case = Cast(COALESCE(f.`Qty per Case`,'12') as SIGNED), 
							i.cost_per_case = Round(Cast(Mid(f.Price,2,InStr(CONCAT(f.Price,' '),' ')-1) as DECIMAL(10,2)),2), 
							i.cost_per_bottle = Round(Cast(Mid(f.Price,2,InStr(CONCAT(f.Price,' '),' ')-1) as DECIMAL(10,2)),2)	/ Cast(COALESCE(f.`Qty per Case`,'12') as SIGNED)";
			$result = mysql_query($sql) or sql_error($sql);	
	}
	
	//Equivalent:  hub_apply_splitcase_to_cost_angels
	public static function hub_apply_splitcase_to_cost_angels() {
		$sql = "UPDATE item_xref SET split_case_charge = '0'
						WHERE left(coalesce(xref,'    '),5) = 'ANGEL'";
		$result = mysql_query($sql) or sql_error($sql);			
	}
	
	//Equivalent:  Angels_update (macro)	
	public static function update() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase_to_cost_angels();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';		
	}
	
	
	//Equivalent:  Angels_insert_compare
	public static function insert_compare() {
	
		$sql = 'INSERT INTO feeds_item_compare ( Source, Producer, Wine, Name, Vintage, `size`, xref, bottles_per_case, catalogid, Region, country, varietal, Appellation, `sub-appellation`, cost, Parker_rating, Spectator_rating, Tanzer_rating, description, store_id, supplier_id )
						SELECT "Feed_Angels" AS Source, u.Winery AS Producer, 
						Left(CONCAT(Left(Winery,5) , Left(`Wine Name`,19) , Right(u.Vintage,2) , "-" , Left(`Size`,3)),30) AS Wine, 
						Left(`Wine Name`,50) AS Name, u.Vintage, 
						if(IsNull(u.size) Or u.size="0","",if(u.size="1500","1.5Ltr",if(u.size="3000","3.0Ltr",if(Cast(u.size as DECIMAL(10,2)) <1000,CONCAT(u.size , "ml"),if(Cast(u.size as DECIMAL(10,2))>=1000,
						CONCAT(CAST(Round(Cast(u.size as DECIMAL(10,2))/1000,3) AS CHAR) , "Ltr"),"Other"))))) AS `size`, 
						CONCAT("ANGEL-",Cast(u.ID as CHAR)) AS xref, coalesce(u.`Qty per Case`,"12") AS bottles_per_case, Null AS catalogid, u.Region, u.Country, u.Varietal, u.appellation, u.`Sub Appelation` AS `sub-appellation`, 
						Round(Cast(Mid(u.Price,2,InStr(CONCAT(u.Price," ")," ")-1) as DECIMAL(10,2)) /Cast(coalesce(u.`Qty per Case`,"12") as SIGNED),2) AS cost, 
						u.`Parker Score`, u.`Spectator Score`, u.`Tanzer Score`, LTrim(CONCAT(u.`Parker Notes` , " " , u.`Spectator Notes` , " " , u.`Tanzer Notes` , " " , u.Notes)), "1", "' . self::get_supplier_id() .'"
						FROM angels_share_feed AS u LEFT JOIN item_xref AS i ON CONCAT("ANGEL-",Cast(u.ID as CHAR))=i.xref
						WHERE isNull(i.xref) And Trim(coalesce(u.`Qty Available`,"")) <> "SOLD OUT" and Trim(coalesce(`Wine Name`,"")) <> "";';
				$res = mysql_query($sql) or sql_error($sql);				

		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	
	
}
