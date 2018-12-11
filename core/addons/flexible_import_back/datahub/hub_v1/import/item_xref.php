<?php
die('no access');
//might not have the right item_xres -> 33668,40072,44195,46220,52756,61781,63400,
//require_once('header.php');
include('constants.php');

//
//$ids_not_delete = '';
//$sql = "SELECT `Item Number` , `Item Name` , LEFT( `Item Name` , LENGTH( `Item Number` ) )
//FROM pos
//WHERE CAST( `Item Number` AS CHAR ) <> LEFT( TRIM( `Item Name` ) , LENGTH( `Item Number` ) )";
//$result_1 = mysql_query($sql) or sql_error($sql);
//while ($row = mysql_fetch_array($result_1)) {
//	$ids_not_delete .= $row['Item Number'] . ",";
//}
//$ids_not_delete  = rtrim($ids_not_delete , ',');
//echo $ids_not_delete ;
//die();
//
//
//$ids_not_delete = '';
//$sql = "SELECT `Item Number` , `Item Name` , LEFT( `Item Name` , LENGTH( `Item Number` ) )
//FROM pos
//WHERE CAST( `Item Number` AS CHAR ) <> LEFT( TRIM( `Item Name` ) , LENGTH( `Item Number` ) )";
//$result_1 = mysql_query($sql) or sql_error($sql);
//while ($row = mysql_fetch_array($result_1)) {
//	$ids_not_delete .= $row['Item Number'] . ",";
//}
//$ids_not_delete  = rtrim($ids_not_delete , ',');
//echo $ids_not_delete;
//die();


//$sql = "Select * from qs2000_item ";
//$result_1 = mysql_query($sql) or sql_error($sql);
//while ($row = mysql_fetch_array($result_1)) {
//	$sql = "Update pos set `Custom Field 5` = '" . mysql_real_escape_string($row['ItemLookupCode']) . "'
//					WHERE `Item Number` = '{$row['ID']}'";
//	mysql_query($sql) or sql_error($sql);	
//}
//die;
//
//////////////////////////
$sql = "Select * from pos  ";
$result_1 = mysql_query($sql) or sql_error($sql);
$ids_not_delete  = '';

while ($row = mysql_fetch_array($result_1)) {
//	$sql = "select * from item_store2 
//					where store_sku = '{$row['Item Number']}'
//					and store_id = '1'";
//	$result_2 = mysql_query($sql) or sql_error($sql);
	//there doesn't seem to be any repeating records with the above query
//	if(mysql_num_rows($result_2) > 1)	{
//		echo $sql . '<br>';
//	}

	//while ($row_2 = mysql_fetch_array($result_2)) {
		$sql = "select * from item_xref
						where item_id = '{$row['Alternate Lookup']}'
						and store_id = '1'";
		$result_3 = mysql_query($sql) or sql_error($sql);
//41 live items that may have the wrong xref		
//		
//			echo "'{$row_2['item_id']}'," . '<br>';
//		}	
		if(mysql_num_rows($result_3) ==  1)	{
			$row_3 = mysql_fetch_array($result_3);
			$row_3['xref'] = trim($row_3['xref']);
			
			if(!empty($row_3['xref']) && $row_3['xref'] != $row['Custom Field 5']) {
			echo  $row['Alternate Lookup'] . '<br>';
					$sql = "UPDATE pos SET 
								 `Custom Field 5` = '" . mysql_real_escape_string($row_3['xref']) . "'
									WHERE `Item Number` = '{$row['Alternate Lookup']}'";
					//mysql_query($sql) or sql_error($sql);
			}
					
		}
		elseif(mysql_num_rows($result_3) >  1) {
			$ids_not_delete  .= $row['Item Number'] . ',';
		}
		
	//}
	
}

$sql = "UPDATE pos set `Qty 1` = '0'";
//mysql_query($sql) or sql_error($sql);

$ids_not_delete  = rtrim($ids_not_delete , ',');
//echo $ids_not_delete ;
die();



//select * from  pos AS qi LEFT JOIN item_store2 AS si ON qi.`Item Number`  = si.store_sku
//LEFT JOIN item_xref AS xf ON ((si.item_id = xf.item_id) and (xf.store_id=1)) LEFT JOIN BevAccessFeeds AS bf ON bf.xref = xf.xref 
//where qi.`Alternate Lookup` in (709634)




$sql = "Select * from xfer_products_SWE where hide = 0";
$result_1 = mysql_query($sql);

while ($row = mysql_fetch_array($result_1)) {
	//we now have the catalogid of live items
//$sql = "select * from item_xref as i
//				INNER JOIN item_store2 as i2
//				ON ((i2.item_id  = i.item_id) AND (i2.store_id  = 1))
//				where i.item_id = {$row['catalogid']} and i.store_id = 1";

	$sql = "select * from item_xref as i				
			where i.item_id = {$row['catalogid']} and i.store_id = 1";

	$result_2 = mysql_query($sql);
	
	while ($row_2 = mysql_fetch_array($result_2)) {
		$sql = "select * from item_store2 as i				
				where i.item_id = {$row_2['catalogid']} and i.store_id = 1";	
			
	}


}

//select * from item_xref as i
//				
//				where i.item_id = '427532' and i.store_id = 1