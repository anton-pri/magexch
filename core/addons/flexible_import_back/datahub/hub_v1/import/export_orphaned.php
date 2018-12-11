<?php
//die('no access');
include('constants.php');
 

//CREATE TABLE IF NOT EXISTS `pos_stranded` (
//  `Item Number` int(11) NOT NULL auto_increment,
//  `Item Name` varchar(255) NOT NULL,
//  `Item Description` text NOT NULL,
//  `Alternate Lookup` varchar(255) NOT NULL,
//  `Attribute` varchar(20) NOT NULL,
//  `Size` varchar(20) NOT NULL,
//  `Average Unit Cost` decimal(19,4) NOT NULL,
//  `Regular Price` decimal(19,4) NOT NULL,
//  `MSRP` decimal(19,4) NOT NULL,
//  `Custom Price 1` decimal(19,4) NOT NULL,
//  `Custom Price 2` decimal(19,4) NOT NULL,
//  `Custom Price 3` decimal(19,4) NOT NULL,
//  `Custom Price 4` decimal(19,4) NOT NULL,
//  `UPC` varchar(255) NOT NULL,
//  `Department Name` varchar(75) NOT NULL,
//  `Department Code` varchar(20) NOT NULL,
//  `Vendor Code` int(11) NOT NULL,
//  `Vendor Name` varchar(255) NOT NULL,
//  `Qty 1` int(11) NOT NULL,
//  `On Order Qty` int(11) NOT NULL default '0',
//  `Reorder Point 1` int(11) NOT NULL default '0',
//  `Custom Field 1` varchar(255) NOT NULL,
//  `Custom Field 2` varchar(255) NOT NULL,
//  `Custom Field 3` int(11) NOT NULL,
//  `Custom Field 4` varchar(10) NOT NULL,
//  `Custom Field 5` varchar(255) NOT NULL,
//  PRIMARY KEY  (`Item Number`),
//  UNIQUE KEY `Custom Field 5_2` (`Custom Field 5`),
//  KEY `Custom Field 5` (`Custom Field 5`),
//  KEY `Alternate Lookup` (`Alternate Lookup`),
//  KEY `Average Unit Cost` (`Average Unit Cost`),
//  KEY `MSRP` (`MSRP`)
//) ENGINE=MyISAM



//$sql = 'select p.*
//from pos as p 
//left join pos_last as l ON l.`Item Number` = p.`Item Number`
//WHERE COALESCE(l.`Item Number`, 0) = 0
//order by  p.`Item Number` desc';


//		$sql = "INSERT IGNORE INTO pos ( 
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

//x.item_id, i.Producer, i.name, i.Vintage, i.Size, i2.store_sku, pr.price

		$sql = "DELETE FROM pos_stranded";
		mysql_query($sql) or sql_error($sql);		
			
//		$sql = "INSERT IGNORE INTO pos_stranded ( 
//						`Item Number`,
//						`Item Name`,
//						`Item Description`,	
//						`Alternate Lookup`,					
//						`Attribute`,
//						`Size`,
//						`Average Unit Cost`,
//						`Regular Price`,
//						`Department Name`,
//						`Vendor Code`,
//						`Custom Field 1`,
//						`Custom Field 2`,
//						`Custom Field 3`,
//						`Custom Field 5`
//						)
//						SELECT DISTINCT
//						i2.store_sku AS Expr1, 
//						Replace(CONCAT(Left(Trim(i.Producer),10) , TRIM(CONCAT(Left(CONCAT(i.name , \" \"),14) , Right(CONCAT(\" \" , i.Vintage),2) , \".\" , Left(CONCAT(i.Size , \" \"),3)))),\" \",\"\") AS Expr2,
//						CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.Vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR)) AS Expr3,
//						x.item_id,
//						i.Vintage AS Expr4,
//						i.size,				
//						x.cost_per_bottle,
//						Round(CAST(x.cost_per_bottle as DECIMAL(19,4))*1.5,0)-0.01,
//						i.country,
//						x.supplier_id,
//						i.Producer,
//						i.varietal,
//						x.bot_per_case,
//						x.xref				
//						
//						FROM item_xref AS x  
//						INNER JOIN item_store2 AS i2 ON ( i2.item_id = x.item_id and x.store_id = 1)
//						INNER JOIN item as i ON x.item_id = i.ID
//						LEFT JOIN pos AS p ON p.`Item Number` = i2.store_sku
//						INNER JOIN item_price AS pr ON x.item_id = pr.item_id and pr.store_id = 1 
//						WHERE    CAST( i2.store_sku AS SIGNED )    > 81208
//						AND x.store_id =1
//						AND COALESCE( p.`Item Number` , 0 ) = 0 order by CAST( i2.store_sku AS SIGNED ) desc";
		
		
		
//		$sql = "INSERT IGNORE INTO pos_stranded ( 
//						`Item Number`,
//						`Item Name`,
//						`Item Description`,	
//						`Alternate Lookup`,					
//						`Attribute`,
//						`Size`,
//						`Average Unit Cost`,
//						`Regular Price`,
//						`Department Name`,
//						`Vendor Code`,
//						`Custom Field 1`,
//						`Custom Field 2`,
//						`Custom Field 3`,
//						`Custom Field 5`
//						)
//						SELECT DISTINCT
//						i2.store_sku AS Expr1, 
//						Replace(CONCAT(Left(Trim(i.Producer),10) , TRIM(CONCAT(Left(CONCAT(i.name , \" \"),14) , Right(CONCAT(\" \" , i.Vintage),2) , \".\" , Left(CONCAT(i.Size , \" \"),3)))),\" \",\"\") AS Expr2,
//						CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.Vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR)) AS Expr3,
//						x.item_id,
//						i.Vintage AS Expr4,
//						i.size,				
//						x.cost_per_bottle,
//						IF(COALESCE(f.xref, '') = '', Round(CAST(x.cost_per_bottle as DECIMAL(19,4))*1.5,0)-0.01, Round(IF(IsNull(`f`.`case_price`) or f.case_price = 0 or f.case_price <= f.bot_price,`f`.`bot_price`,`f`.`case_price`/`f`.`botpercase`)*1.5,0)-0.01),
//						i.country,
//						x.supplier_id,
//						i.Producer,
//						i.varietal,
//						x.bot_per_case,
//						x.xref				
//						
//						FROM item_xref AS x  
//						INNER JOIN item_store2 AS i2 ON ( i2.item_id = x.item_id and x.store_id = 1)
//						INNER JOIN item as i ON x.item_id = i.ID
//						LEFT JOIN pos AS p ON p.`Item Number` = i2.store_sku
//						INNER JOIN item_price AS pr ON x.item_id = pr.item_id and pr.store_id = 1 
//						LEFT JOIN BevAccessFeeds AS f ON f.xref = x.xref	
//						WHERE    CAST( i2.store_sku AS SIGNED )    > 81208
//						AND x.store_id =1
//						AND COALESCE( p.`Item Number` , 0 ) = 0 order by CAST( i2.store_sku AS SIGNED ) desc	";		
//
//		mysql_query($sql) or sql_error($sql);

		pos::binlocation_to_id();
 
		$sql = "INSERT IGNORE INTO pos_stranded ( 
						`Item Number`,
						`Item Name`,
						`Item Description`,	
						`Alternate Lookup`,					
						`Attribute`,
						`Size`,
						`Average Unit Cost`,
						`Regular Price`,
						`Department Name`,
						`Vendor Code`,
						`Custom Field 1`,
						`Custom Field 2`,
						`Custom Field 3`,
						`Custom Field 5`
						)
						SELECT DISTINCT
						i2.store_sku AS Expr1, 
						Replace(CONCAT(Left(Trim(i.Producer),10) , TRIM(CONCAT(Left(CONCAT(i.name , \" \"),14) , Right(CONCAT(\" \" , i.Vintage),2) , \".\" , Left(CONCAT(i.Size , \" \"),3)))),\" \",\"\") AS Expr2,
						CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.Vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR)) AS Expr3,
						x.item_id,
						i.Vintage AS Expr4,
						i.size,				
						x.cost_per_bottle,
						IF(COALESCE(f.xref, '') = '', Round(CAST(x.cost_per_bottle as DECIMAL(19,4))*1.5,0)-0.01, Round(IF(IsNull(`f`.`case_price`) or f.case_price = 0 or f.case_price <= f.bot_price,`f`.`bot_price`,`f`.`case_price`/`f`.`botpercase`)*1.5,0)-0.01),
						i.country,
						x.supplier_id,
						i.Producer,
						i.varietal,
						x.bot_per_case,
						x.xref				
						
						FROM item_xref AS x  
						INNER JOIN item_store2 AS i2 ON ( i2.item_id = x.item_id and x.store_id = 1)
						INNER JOIN item as i ON x.item_id = i.ID
						LEFT JOIN pos AS p ON p.`Alternate Lookup`  = i.ID
						INNER JOIN item_price AS pr ON x.item_id = pr.item_id and pr.store_id = 1 
						LEFT JOIN BevAccessFeeds AS f ON f.xref = x.xref	
						WHERE    i.ID   > 0
						AND x.store_id =1
						AND COALESCE( p.`Alternate Lookup` , 0 ) = 0";

		mysql_query($sql) or sql_error($sql);		
		
		pos::binlocation_to_varchar();
		
		//sometimes probs with dup_catid in item table
		//perhaps it can be solved with the query above but this works for now
		$sql = "DELETE FROM pos_stranded
						WHERE `Item Number` 
						IN (
							SELECT  `Item Number`  FROM pos
						)";		
		mysql_query($sql) or sql_error($sql);	

		//update Item Name
		$sql = "UPDATE pos_stranded
						SET `Item Name` = CONCAT(`Item Number` , ' ', `Item Name`)
						WHERE CAST(TRIM(`Item Number`) as CHAR)  <>  LEFT(TRIM(`Item Name`), LENGTH(`Item Number`))";
		mysql_query($sql) or sql_error($sql);	
//add MSRP and Custom Price 1
		$sql = "UPDATE (item_store2 AS si 
						INNER JOIN pos_stranded AS q ON si.store_sku = q.`Item Number`) 
						INNER JOIN item_price AS pr ON si.item_id = pr.item_id and pr.store_id = 1 
						SET q.`MSRP` = pr.price,
						q.`Custom Price 1` = pr.price
						WHERE si.store_id = 1";	
		mysql_query($sql) or sql_error($sql);	
//add vendor name		
		$sql = "UPDATE pos_stranded as ps
						INNER JOIN Supplier as s
						ON s.supplier_id = ps.`Vendor Code`
						SET ps.`Vendor Name` = s.SupplierName";						
		mysql_query($sql) or sql_error($sql);	
	//delete anything with no cost
	$sql = "DELETE FROM pos_stranded WHERE COALESCE(`Average Unit Cost`, 0) = 0";
	mysql_query($sql) or sql_error($sql);		
	
	$sql = 'SELECT  *
					FROM pos_stranded  
					ORDER BY `Item Number` DESC';

	$ts =  date('Y-m-d__H-i-s');
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0) {
		exportMysqlToXls('pos_stranded', "stranded_$ts.xls", $sql);
	}
	else {
		echo 'no new records to export';
	}


?>
