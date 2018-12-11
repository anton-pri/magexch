<?php
//require_once('header.php');
die('no access');
include('constants.php');

$sql = "INSERT into pos (`Item Number`, `Item Name`, `Item Description`, `Alternate Lookup`, `Average Unit Cost`, `Regular Price`, `MSRP`, `Custom Price 1`, `Custom Price 2`, 
`Custom Price 3` , `Vendor Code`, `Custom Field 4`, `Custom Field 5`

)
select qs.ID, qs.Description, qs.Notes, qs.BinLocation, qs.Cost, qs.Price, qs.PriceA, qs.SalePrice, qs.PriceB, qs.PriceC, qs.SupplierID, qs.PictureName, qs.ItemLookupCode
from qs2000_item as qs";

mysql_query($sql) or sql_error($sql);	

//$sql = "select i.* from item as i
//				INNER JOIN qs2000_item as qs
//				ON i.ID = qs.binlocation limit 5";

$sql = "select * from item as i
				INNER JOIN item_store2 as i2
				ON ((i2.item_id  = i.ID) AND (i2.store_id  = 1))";

$result = mysql_query($sql) or sql_error($sql);	
//`Alternate Lookup` = '{$row['ID']}'	
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$sql = " UPDATE pos
						SET 
						`Department Name` =  '" . mysql_real_escape_string($row['Producer']) . "',
						`Attribute` = '" . mysql_real_escape_string($row['Vintage']) . "',								
						`Size` = '" . mysql_real_escape_string($row['Size']) . "',				
						`Department Name` = '" . mysql_real_escape_string($row['country']) . "',										
						`Custom Field 1` = '" . mysql_real_escape_string($row['Producer']) . "',	
						`Custom Field 2` = '" . mysql_real_escape_string($row['varietal']) . "',	
						`Custom Field 3` = '" . mysql_real_escape_string($row['bot_per_case']) . "'
						WHERE 
						 `Item Number` = '{$row['store_sku']}'
																
						";
	mysql_query($sql) or sql_error($sql);		
	//echo $sql;die;
	//print_r($row);die;
}
 
$sql = "select * from  pos";
$result = mysql_query($sql) or sql_error($sql);	
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$sql = "select * from Supplier where supplier_id = '{$row['Vendor Code']}'";
	$res = mysql_query($sql) or sql_error($sql);		
	$r = mysql_fetch_array($res, MYSQL_ASSOC);
	
	$sql = "update pos set
					`Vendor Name` = '" . mysql_real_escape_string($r['SupplierName']) . "'
					WHERE `Item Number` = '{$row['Item Number']}'"; 
 	mysql_query($sql) or sql_error($sql);	
}

$sql = "select * from item_xref as i
				INNER JOIN item_store2 as i2
				ON ((i2.item_id  = i.item_id) AND (i2.store_id  = 1))
				where i.store_id = 1";

$result = mysql_query($sql) or sql_error($sql);	
//`Alternate Lookup` = '{$row['ID']}'	


while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

	if(!empty($row['xref'])) {
		$sql = " UPDATE pos
							SET 
							`Custom Field 5` = '" . mysql_real_escape_string($row['xref']) . "'
							WHERE 
							 `Item Number` = '{$row['store_sku']}'
																	
							";
		mysql_query($sql) or sql_error($sql);	
	}

}

$sql = "update pos 
set `Vendor Code` = '88',
`Vendor Name` = 'Empire Merchants North'
where `Vendor Code` = '10'";
mysql_query($sql)or sql_error($sql);	

$sql = "select * from  pos";
$result = mysql_query($sql) or sql_error($sql);	
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	exportMysqlToCsv('pos', 'one_time.csv', $sql);
}
else {
	echo 'no new records to export';
}