<?php
/**
 * this has table_id
 * this can be run on create
 
 * 
 * ALTER TABLE `cw_import_feed` ADD PRIMARY KEY ( `ID` );
 */

class cw_import_feed extends feed {
	public static $table = 'cw_import_feed';
	public static $feed_file = '';
/*  supplier_id is dynamic, defined in flex import profile mapping	
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
*/	
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
						Replace(CONCAT(Left(Trim(c.Producer),10) , TRIM(CONCAT(Left(CONCAT(c.name , \" \"),14) , Right(CONCAT(\" \" , IF(c.Vintage='','NV',c.Vintage)),2) , \".\" , Left(CONCAT(c.Size , \" \"),3)))),\" \",\"\") AS Expr2, 
						'' AS Expr3,					
						c.Vintage AS Expr4,
						c.size,
						Round(c.cost,2) AS Expr5,
						Round(CAST(c.cost as DECIMAL(19,4))*1.5,0)-0.01,
                                                IF((INSTR('".$config['flexible_import']['fi_spirit_varietals']."',c.varietal) AND c.varietal!=''),'Spirit','Wine'),
						c.country,
						(SELECT SupplierName FROM Supplier WHERE supplier_id = c.supplier_id),
						c.Producer,
						c.varietal,
						c.bottles_per_case,
						c.xref
						FROM feeds_item_compare AS c LEFT JOIN pos AS qi ON COALESCE(c.catalogid,0) = qi.`Alternate Lookup`
						WHERE (c.store_id = 1 and Left(c.xref,2) = 'CW' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
		mysql_query($sql) or sql_error($sql);		
		pos::binlocation_to_varchar();			
	}		
	
	public static function table_name() {
		return self::$table;
	}
	
	
	public static function set_qty_0() {
		$sql = "UPDATE item_xref AS i SET qty_avail = '0'
						WHERE LEFT(coalesce(i.xref,'      '),2) = 'CW'";

		mysql_query($sql) or sql_error($sql);	
	}
	
	public static function update_item_xref() {

			$sql = "UPDATE (item_xref AS i 
                      INNER JOIN cw_import_feed AS u ON i.xref=CONCAT('CW',u.feed_short_name,'-',Cast(u.ITEMID as CHAR),'-',IF(Right(u.Vintage,2)='','NV',Right(u.Vintage,2))))
                      SET i.qty_avail = u.item_xref_qty_avail, 
                          i.min_price = u.item_xref_min_price, 
                          i.bot_per_case = u.item_xref_bot_per_case, 
                          i.cost_per_case = u.item_xref_cost_per_case, 
                          i.cost_per_bottle = u.item_xref_cost_per_bottle";
			$result = mysql_query($sql) or sql_error($sql);	
	}
	
	//Equivalent:  hub_apply_splitcase_to_cost_cw_import
	public static function hub_apply_splitcase_to_cost_cw_import() {
		$sql = "UPDATE item_xref ixrf SET split_case_charge = IF(ixrf.xref LIKE 'CWOPC-%', IF(ixrf.bot_per_case>1,(9/ixrf.bot_per_case),0),(SELECT charge FROM splitcase_charges WHERE CONCAT('CW',company) = split_string(ixrf.xref,'-',1) limit 1)) WHERE xref like 'CW%'";
		$result = mysql_query($sql) or sql_error($sql);			
	}
	
	public static function update() {
		self::set_qty_0();
		self::update_item_xref();
		self::hub_apply_splitcase_to_cost_cw_import();
		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';		
	}

        public static function update_only() {
                self::set_qty_0();
                self::update_item_xref();
                self::hub_apply_splitcase_to_cost_cw_import();
                echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';
        }
	
	public static function insert_compare() {
$sql = 'INSERT INTO feeds_item_compare (
Source,
wholesaler,
Wine,
Producer,
Name,
Vintage,
`size`,
xref,
catalogid,
bottles_per_case,
cost,
country,
Region,
varietal,
Appellation,
`sub-appellation`,
Parker_rating,
Parker_review,
Spectator_rating,
Spectator_review,
Tanzer_rating,
Tanzer_review,
`W&S_rating`,
`W&S_review`,
Description,
store_id,    
qty_in_stock,
supplier_id) 
SELECT u.Source,
u.wholesaler,
u.Wine,
u.Producer,
u.Name,
u.Vintage,
u.`size`,
CONCAT("CW",u.feed_short_name,"-",Cast(u.ITEMID as CHAR),"-",IF(Right(u.Vintage,2)="","NV",Right(u.Vintage,2))),
NULL,
u.bottles_per_case,
u.cost,
u.country,
u.Region,
u.varietal,
u.Appellation,
u.`sub-appellation`,
u.Parker_rating,
u.Parker_review,
u.Spectator_rating,
u.Spectator_review,
u.Tanzer_rating,
u.Tanzer_review,
u.`W&S_rating`,
u.`W&S_review`,
"",
1,    
u.qty_in_stock,
u.supplier_id 
FROM cw_import_feed AS u LEFT JOIN item_xref AS i ON CONCAT("CW",u.feed_short_name,"-",Cast(u.ITEMID as CHAR),"-",IF(Right(u.Vintage,2)="","NV",Right(u.Vintage,2)))=i.xref
WHERE isNull(i.xref) and Trim(coalesce(u.Name,"")) <> ""';

				$res = mysql_query($sql) or sql_error($sql);				

		echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
	}	
}
