<?php
/**
 ALTER TABLE `pricing_aipo` CHANGE `aipo` `aipo` DOUBLE( 16, 13 ) NULL DEFAULT '0';
 //this table will evenutally need store_id
*/
class pricing_aipo extends feed {
	public static $table = 'pricing_aipo';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
	
	public static function delete_table() {
		$sql = "DELETE FROM " . self::table_name();
		mysql_query($sql) or sql_error($sql);		
	}	
	
//Equivalent:  pricing_calc_aaipo
/**
 * @todo make this for the other stores
 *
 */
	public static function pricing_calc_aaipo() {
		$sql = "INSERT INTO pricing_aipo
						SELECT b.ID AS item_id, avg(a.aaipo) AS aipo
						FROM (select i.ID, avg(COALESCE(sf.Cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0))) as acost
						      from ((item i
						      inner join item_store2 si on i.ID = si.item_id)
						      left join SWE_store_feed sf on (si.store_sku = sf.sku and si.store_id = 1))
						      left join item_xref xf on (si.item_id = xf.item_id and si.store_id = xf.store_id)
						group by i.ID) AS b LEFT JOIN (select acost, avg(aipo) as aaipo from (
						                                        select round(avg_price,0) as acost, avg_items_per_order * avg_line_items_per_order as aipo
						                                        from pricing_IPO_avg_price) as x group by acost
						                                       ) AS a ON (b.acost < a.acost*1.3) AND (b.acost > a.acost*.7)
						WHERE COALESCE(b.acost,0) > 0
						GROUP BY b.ID";
		mysql_query($sql) or sql_error($sql);			
	}
	
//this works for dwb
//INSERT INTO pricing_aipo
//SELECT b.ID AS item_id, avg(a.aaipo) AS aipo
//FROM (select i.ID, avg(COALESCE(sf.Cost, xf.cost_per_bottle + COALESCE(xf.split_case_charge,0))) as acost
//      from ((item i
//      inner join item_store2 si on i.ID = si.item_id)
//      left join SWE_store_feed sf on (si.store_sku = sf.sku and si.store_id = 1))
//      left join item_xref xf on (si.item_id = xf.item_id and si.store_id = xf.store_id)
//group by i.ID) AS b LEFT JOIN (select acost, avg(aipo) as aaipo from (
//                                        select round(avg_price,0) as acost, avg_items_per_order * avg_line_items_per_order as aipo
//                                        from pricing_IPO_avg_price) as x group by acost
//                                       ) AS a ON (b.acost < a.acost*1.3) AND (b.acost > a.acost*.7)
//WHERE COALESCE(b.acost,0) > 0
//GROUP BY b.ID;
	
}//end class