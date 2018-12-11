<?php
die('no access');
include('constants.php');
//$sql = 'select p.*
//from pos as p 
//left join pos_last as l ON l.`Item Number` = p.`Item Number`
//WHERE COALESCE(l.`Item Number`, 0) = 0
//order by  p.`Item Number` desc';

//
//		$foo = "INSERT INTO pos ( 
//						`Item Number`,
//						`Item Name`,
//						`Item Description`,						
//						`Attribute`,
//						`Size`,
//						`Average Unit Cost`,
//						`Regular Price`,
//						`Department Name`,
//						`Vendor Name`,
//						`Custom Field 1`,
//						`Custom Field 2`,
//						`Custom Field 3`,
//						`Custom Field 5`
//						)
//						SELECT DISTINCT
//						'' AS Expr1,
//						Replace(CONCAT(Left(Trim(c.Producer),10) , TRIM(CONCAT(Left(CONCAT(c.name , \" \"),14) , Right(CONCAT(\" \" , c.Vintage),2) , \".\" , Left(CONCAT(c.Size , \" \"),3)))),\" \",\"\") AS Expr2, 
//						'' AS Expr3,					
//						c.Vintage AS Expr4,
//						c.size,
//						Round(c.cost,2) AS Expr5,
//						Round(CAST(c.cost as DECIMAL(19,4))*1.5,0)-0.01,
//						c.country,
//						'" . mysql_real_escape_string(self::get_supplier_name()) . "',
//						c.Producer,
//						c.varietal,
//						c.bottles_per_case,
//						c.xref
//						FROM feeds_item_compare AS c LEFT JOIN pos AS qi ON COALESCE(c.catalogid,0) = qi.`Alternate Lookup`
//						WHERE (c.store_id = 1 and Left(c.xref,4) = 'TOUT' and COALESCE(qi.`Alternate Lookup`, 0) = 0 and (c.add_to_hub=true or COALESCE(c.catalogid, 0) > 0 ));";
//
//
//$sql = 'SELECT i.*, x.*, i2.*, x.bot_per_case as botty,  x.supplier_id as sid, i2.item_id as the_id
//		FROM item AS i
//		LEFT JOIN item_xref AS x ON ( i.ID = x.item_id )
//		INNER JOIN item_store2 AS i2 ON ( i2.item_id = x.item_id )
//		LEFT JOIN pos_snap_shot AS p ON p.`Item Number` = i2.store_sku
//		WHERE    CAST( i2.store_sku AS SIGNED )    > 81205
//		AND x.store_id =1
//		AND COALESCE( p.`Item Number` , 0 ) = 0 order by CAST( i2.store_sku AS SIGNED ) desc';
//
//$result = mysql_query($sql) or sql_error($sql);
//while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//		$row['store_sku'] = mysql_real_escape_string($row['store_sku']);
//		$row['Producer'] = mysql_real_escape_string($row['Producer']);
//		$row['name'] = mysql_real_escape_string($row['name']);
//		$row['Vintage'] = mysql_real_escape_string($row['Vintage']);
//		$row['Size'] = mysql_real_escape_string($row['Size']);
//		$row['cost_per_bottle'] = mysql_real_escape_string($row['cost_per_bottle']);
//		$row['country'] =  mysql_real_escape_string($row['country']);
//		$row['varietal'] =  mysql_real_escape_string($row['varietal']);	
//		$row['botty'] =  mysql_real_escape_string($row['botty']);
//		$row['xref'] =  mysql_real_escape_string($row['xref']);					
//		$row['the_id'] =  mysql_real_escape_string($row['the_id']);		
//			
//	
//		$sql = "select * from Supplier where supplier_id = '{$row['sid']}'";	
//		$res = mysql_query($sql) or sql_error($sql);
//		$r = mysql_fetch_array($res);
//		$r['SupplierName']=  mysql_real_escape_string($r['SupplierName']);		
//		
//
//		$sql = "INSERT INTO pos_export ( 
//						`Item Number`,
//						`Item Name`,
//						`Item Description`,	
//						`Alternate Lookup`,					
//						`Attribute`,
//						`Size`,
//						`Average Unit Cost`,
//						`Regular Price`,
//						`Department Name`,
//						`Vendor Name`,
//						`Custom Field 1`,
//						`Custom Field 2`,
//						`Custom Field 3`,
//						`Custom Field 5`
//						)
//						values(
//						'{$row['store_sku']}',
//						Replace(CONCAT(Left(Trim(\"{$row['Producer']}\"),10) , TRIM(CONCAT(Left(CONCAT(\"{$row['name']}\" , \" \"),14) , Right(CONCAT(\" \" , \"{$row['Vintage']}\"),2) , \".\" , Left(CONCAT(\"{$row['Size']}\" , \" \"),3)))),\" \",\"\"), 
//						'' ,	
//						'{$row['the_id']}',				
//						'{$row['Vintage']}',
//						'{$row['Size']}',
//						Round({$row['cost_per_bottle']},2) ,
//						Round(CAST({$row['cost_per_bottle']} as DECIMAL(19,4))*1.5,0)-0.01,
//						'{$row['country']}',
//						'{$r['SupplierName']}',
//						'{$row['Producer']}',
//						'{$row['varietal']}',
//						'{$row['botty']}',
//						'{$row['xref']}'
//);";
// mysql_query($sql) or sql_error($sql);
////echo $sql . '<br>';
//}
//die('sdfsdf');
pos::hub_update_POS_notes();
die;
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	exportMysqlToXls('pos', 'new.xls', $sql);
}
else {
	echo 'no new records to export';
}


?>
