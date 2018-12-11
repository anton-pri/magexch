<?php
include('constants.php');
//$sql = 'select p.*
//from pos as p 
//left join pos_last as l ON l.`Item Number` = p.`Item Number`
//WHERE COALESCE(l.`Item Number`, 0) = 0
//order by  p.`Item Number` desc';


$sql = 'select p.*
from pos as p 
left join pos_snap_shot as l ON l.`Item Number` = p.`Item Number`
WHERE COALESCE(l.`Item Number`, 0) = 0
order by  p.`Item Number` desc';

$ts =  date('Y-m-d__H-i-s');
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	exportMysqlToXls('pos', "new_$ts.xls", $sql);
}
else {
	echo 'no new records to export';
}


?>
