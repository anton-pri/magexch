<?php
include('constants.php');

//$sql = 'SELECT p.* from pos as p
//				INNER JOIN pos_snap_shot as l ON l.`Item Number` = p.`Item Number`
//				INNER JOIN  xfer_products_SWE as x ON x.catalogid = p.`Alternate Lookup`
//				WHERE (
//				TRIM(p.`Average Unit Cost`) <> TRIM(l.`Average Unit Cost`) 
//				OR TRIM(p.`MSRP`) <> TRIM(l.`MSRP`)
//				OR TRIM(p.`Size`) <> TRIM(l.`Size`)
//				OR TRIM(p.`Department Name`) <> TRIM(l.`Department Name`)
//				OR TRIM(p.`Custom Field 1`) <> TRIM(l.`Custom Field 1`)
//				OR TRIM(p.`Custom Field 2`) <> TRIM(l.`Custom Field 2`)
//				OR TRIM(p.`Custom Field 3`) <> TRIM(l.`Custom Field 3`)
//				) 
//				AND x.hide = 0';

$sql = 'select p.* from pos as p
inner join pos_snap_shot as l ON l.`Item Number` = p.`Item Number`
inner join  xfer_products_SWE as x ON x.catalogid = p.`Alternate Lookup`
WHERE (
trim(p.`Average Unit Cost`) <> trim(l.`Average Unit Cost`) 
OR trim(p.`MSRP`) <> trim(l.`MSRP`)
OR trim(p.`Size`) <> trim(l.`Size`)
OR trim(p.`Department Name`) <> trim(l.`Department Name`)
OR trim(p.`Custom Field 1`) <> trim(l.`Custom Field 1`)
OR trim(p.`Custom Field 2`) <> trim(l.`Custom Field 2`)
OR trim(p.`Custom Field 3`) <> trim(l.`Custom Field 3`)

OR trim(p.`Custom Price 1`) <> trim(l.`Custom Price 1`)
OR trim(p.`Regular Price`) <> trim(l.`Regular Price`)
OR trim(p.`Vendor Code`) <> trim(l.`Vendor Code`)
OR trim(p.`Custom Field 5`) <> trim(l.`Custom Field 5`)
) AND x.hide = 0';

$ts =  date('Y-m-d__H-i-s');
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	exportMysqlToXls('pos', "changed_$ts.xls", $sql);
}
else {
	echo 'no new records to export';
}


?>
