<?php
/**
 * Shell tool to check database integrity by primary keys
 * @see PK definition in pk.ini
 *
 * @use To check integrity
 * > php index.php <cron_code> pk 
 *
 * @use To fix integrity by deleting of orphaned records
 * > php index.php <cron_code> pk --delete
 */

$fk = parse_ini_file($app_main_dir.'/cron/pk.ini', true);
$msg = array();
echo $msg[]="Check primary keys integrity based on rules in pk.ini\n";
foreach ($fk as $pt => $t) {
	list($primary_table, $primary_key) = explode('.',$pt);
	if (empty($primary_table) || empty($primary_key)) continue;
	
	foreach ($t as $secondary_table=>$secondary_key) {
		list($secondary_key, $secondary_where) = explode('|',$secondary_key);
		$secondary_where = str_replace($secondary_table,'sec',$secondary_where);
		if (empty($secondary_key)) {
			$secondary_key = $primary_key;
		}
		if (empty($secondary_where)) {
			$secondary_where = '1';
		}		
		$missed_ids = cw_query_column($q="SELECT sec.$secondary_key
		FROM $secondary_table sec
		LEFT JOIN $primary_table prim ON sec.$secondary_key=prim.$primary_key
		WHERE sec.$secondary_key!='0' AND sec.$secondary_key!='' AND prim.$primary_key IS NULL AND $secondary_where");
		$missed = count($missed_ids);
		if ($missed == 0) continue;
		echo $msg[]="$missed orphaned records in $secondary_table.$secondary_key points to $primary_table.$primary_key \n";
		echo $msg[]=join(', ',array_unique($missed_ids))."\n";

		if ($argv[3] == '--delete') {
			db_exec("DELETE FROM $secondary_table WHERE $secondary_key IN(?) AND $secondary_where",array(array_unique($missed_ids)));
			echo $msg[]="Deleted\n";
		}
		echo "\n";

	}
	
}

return $msg;
