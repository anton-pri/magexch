<?php

$sql = "SELECT xf.catalogid FROM xfer_products_SWE as xf
				INNER JOIN xfer_products_SWE_snapshot as sn
				ON xf.catalogid = sn.catalogid
				WHERE (xf.hide = 1 AND sn.hide = 0)";
				
$result = mysql_query($sql) or sql_error($sql);				
if(mysql_num_rows($result) > 0) {
	$string = '';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) { 
		$string .= "{$row['catalogid']},";
	}
	$string = rtrim($string, ',');
	$sql = "DELETE  FROM xfer_products_SWE  
					WHERE xfer_products_SWE.hide = 1 and xfer_products_SWE.catalogid NOT IN
					(
						$string
					)";
}
else {
	$sql = "DELETE  FROM xfer_products_SWE  
					WHERE xfer_products_SWE.hide = 1";


} 
mysql_query($sql) or sql_error($sql);		

$sql = "DELETE FROM xfer_products_SWE_snapshot";		
mysql_query($sql) or sql_error($sql);		

$sql = "INSERT INTO xfer_products_SWE_snapshot
				SELECT * FROM xfer_products_SWE";		
mysql_query($sql) or sql_error($sql);	