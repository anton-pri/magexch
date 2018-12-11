<?php
/**
 	ALTER TABLE `item` ADD INDEX ( `Producer` );
 	ALTER TABLE `item` ADD INDEX ( `name` ) ; 
	ALTER TABLE `item` ADD INDEX ( `dup_catid` ) ; 
	ALTER TABLE `item` ADD INDEX ( `initial_xref` ) ; 	
 *
 */
class item extends feed {
	public static $table = 'item';
	public static $feed_file = '';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  pricing_apply_surcharges	
	public static function pricing_apply_surcharges() {
		$sql = "UPDATE (hub_price_settings AS ps INNER JOIN item_price AS i ON ps.store_id = i.store_id) 
						INNER JOIN item AS i2 ON i.item_id = i2.ID 
						SET i.price = i.price + ps.oversize_surcharge
						WHERE i2.TareWeight >= 6";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  compare_update_item
	public static function compare_update_item() {
		$sql = "UPDATE item AS i INNER JOIN feeds_item_compare AS c ON i.ID = c.catalogid 
						SET i.Producer = c.Producer,
						i.name = c.Name,
						i.Vintage = c.Vintage,
						i.`Size` = c.Size,
						i.country = c.country,
						i.region = c.region,
						i.varietal = c.varietal,
						i.appellation = c.appellation,
						i.`sub-appellation` = `c`.`sub-appellation`,
						i.bot_per_case = c.bottles_per_case,
						i.dup_catid = c.dup_catid,
						i.`RP Rating` = c.Parker_rating,
						i.`RP Review` = c.Parker_review,
						i.`WS Rating` = c.Spectator_rating,
						i.`WS Review` = c.Spectator_review,
						i.`ST Rating` = c.Tanzer_rating,
						i.`ST Review` = c.Tanzer_review
						WHERE c.Source = 'Hub'";
		mysql_query($sql) or sql_error($sql);				
	}
//Equivalent:  hub_copy_images_from_similar_wines
	public static function hub_copy_images_from_similar_wines() {
		$sql = "UPDATE item AS i 
						INNER JOIN item AS i2 ON i.Producer = i2.Producer and i.name = i2.name and i.ID <> i2.ID and i2.cimageurl = 'images/no_image.jpg' and i.cimageurl <> 'images/no_image.jpg' 
						SET i2.cimageurl = i.cimageurl, i2.extendedimage = i.extendedimage
						WHERE Trim(COALESCE(i.Producer,'')) <> '' and Trim(COALESCE(i.name,'')) <> ''";
		mysql_query($sql) or sql_error($sql);				
	}

//Equivalent:  set_image_to_no_image_if_blank
	public static function set_image_to_no_image_if_blank() {
		$sql = "UPDATE item SET cimageurl = 'images/no_image.jpg', extendedimage = 'images/no_image.jpg'
						WHERE cimageurl = '' or isnull(cimageurl) or Instr(cimageurl,'placeholder')";
		mysql_query($sql) or sql_error($sql);				
	}
	
	public static function update_longdesc() {
		$sql = "UPDATE item as i
						INNER JOIN item_xref AS x ON x.item_id = i.ID
						INNER JOIN wildman_feed AS wld ON replace(x.xref, 'WDMN-', '') = wld.`Item No`
						SET i.LongDesc = wld.Ratings
						WHERE COALESCE(i.LongDesc, '')  = '' 
						and x.xref like '%WDMN-%'
						and upper(x.xref) NOT REGEXP '[A-Z]'";

		mysql_query($sql) or sql_error($sql);								
	}
	
	
}//end class