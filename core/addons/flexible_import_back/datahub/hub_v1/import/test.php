<?php
die('no access');
require_once('header.php');


	function dist_strip($string) {
		return preg_replace('/[^a-zA-Z]/', '', $string);
  }	
  
	function get_dist($comp, $comp2) {
//		$dom = substr($comp2, 5, 1);
//		if($dom == 'D' || $dom == 'd') {
//			$comp = 'DOMAIN';
//		}

//		if(strlen($comp) < 3) {
//			return 'Swe';
//		}

		$comp = trim($comp);
		if(empty($comp)) {
			return 'none found';
		}
		$sql = "SELECT * 
						FROM distributors
						WHERE company like '%$comp%' LIMIT 1";
		echo $sql . '<br>';
		$result = mysql_query($sql) or sql_error($sql);		
		$row = array();
		if(mysql_num_rows($result) > 0) {
			//$count--;
			$row = mysql_fetch_array($result);
			return $row['pos_supplier_id'];
		}		
		

		return 'none found';
  }	 
  
  

$sql = "SELECT * 
				FROM  `item_xref` 
				WHERE COALESCE(  `xref` ,  '' ) <>  ''
				AND store_id =2";
$result = mysql_query($sql) or sql_error($sql);	
//$count = mysql_num_rows($result);
//echo $count . '<br/>';

while ($row = mysql_fetch_array($result)) {
	$x = '';
	$dist = '';
	$array =  explode('-', $row['xref']);
	$dist = $array[0];
//		$dom = substr($array[0], 5, 1);
//		if($dom == 'D' || $dom == 'd') {
//			$dist = 'DOMAIN';
//		}	
	//$dist = dist_strip($dist);
	//echo $dist . '<br>';
	
	if(strlen($dist) < 2) {
		continue;
	}
	
	if($dist != 'DOMAIN') {
		$list = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$dist = str_replace($list, '', $dist);	
	}

		
		$dist = trim($dist);
		if(empty($dist)) continue;
		
		$sql = "SELECT * 
						FROM Supplier
						WHERE SupplierName like '%$dist%' LIMIT 1";

		$res = mysql_query($sql) or sql_error($sql);		
	


	if(mysql_num_rows($res) > 0) {
		$r = mysql_fetch_array($res);
		$sql = "UPDATE item_xref
						SET supplier_id = '{$r['supplier_id']}'
						WHERE item_id = '{$row['item_id']}' AND xref = '{$row['xref']}'";
		//echo $sql . '<br>';		
		mysql_query($sql) or sql_error($sql);	
	}
//	if($row['item_id'] == '426273') {
//		echo $x;
//		die;
//	}	

	//echo $row['xref'] . ' -> ' . $x . ' <br>';
}
//echo $count;


//$sql = "SELECT * FROM item_xref as i
//				INNER JOIN qs2000_item as q ON i.item_id = q.BinLocation
//				WHERE COALESCE(q.BinLocation, '') <> ''
//				AND COALESCE(q.SupplierID, 0) <> 0 
//				AND i.store_id = 1";



$sql = "select * from
			(( pos AS qi 
			inner JOIN item_store2 AS si ON qi.`Item Number` = si.store_sku) 
			inner JOIN item_xref AS xf ON (si.item_id = xf.item_id) and (xf.store_id=1))";

$result = mysql_query($sql) or sql_error($sql);	
//$count = mysql_num_rows($result);
//echo $count . '<br/>';
while ($row = mysql_fetch_array($result)) {
	//$sql = "SELECT * FROM distributors WHERE pos_supplier_id = '{$row['SupplierID']}'";
	$sql = "SELECT * FROM Supplier WHERE supplier_id = '{$row['Vendor Code']}'";
	$res = mysql_query($sql) or sql_error($sql);	
	$r = mysql_fetch_array($res);
	if(mysql_num_rows($res) > 0) {
		$sql = "UPDATE item_xref
						SET supplier_id = '{$r['supplier_id']}'
						WHERE item_id = '{$row['item_id']}' AND xref = '{$row['xref']}'";
		mysql_query($sql) or sql_error($sql);		
		//$count--;
	}
}
//echo $count;

$sql = "update item_xref 
set supplier_id = 88
where supplier_id = 10";
mysql_query($sql) or sql_error($sql);	

die('done supplier id');

/////
//this is for the new pos


$sql = "select * from
			(( pos AS qi 
			inner JOIN item_store2 AS si ON qi.`Item Number` = si.store_sku) 
			inner JOIN item_xref AS xf ON (si.item_id = xf.item_id) and (xf.store_id=1))";

$result = mysql_query($sql) or sql_error($sql);	
//$count = mysql_num_rows($result);
//echo $count . '<br/>';
while ($row = mysql_fetch_array($result)) {
	//$sql = "SELECT * FROM distributors WHERE pos_supplier_id = '{$row['SupplierID']}'";
	$sql = "SELECT * FROM Supplier WHERE supplier_id = '{$row['Vendor Code']}'";
	$res = mysql_query($sql);
	$r = mysql_fetch_array($res);
	if(mysql_num_rows($res) > 0) {
		$sql = "UPDATE item_xref
						SET supplier_id = '{$r['supplier_id']}'
						WHERE item_id = '{$row['item_id']}' AND xref = '{$row['xref']}'";
		mysql_query($sql) or sql_error($sql);			
		//$count--;
	}
}

$sql = "update item_xref 
set supplier_id = 88
where supplier_id = 10";
mysql_query($sql)or sql_error($sql);	

//will be removing this
$sql = "update pos as p 
inner join qs2000_item as q
on p.`Item Number` = q.ID
set p.`Qty 1` = q.Quantity";
mysql_query($sql)or sql_error($sql);	