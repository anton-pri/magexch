<?php
/**
 * Shell tool to check images in DB and filesystem
 *
 * @use To check integrity
 * > php index.php <cron_code> images 
 * @use To delete discrepancy on both sides or only in DB or FS
 * > php index.php <cron_code> images --delete
 * > php index.php <cron_code> images --delete-fs
 * > php index.php <cron_code> images --delete-db
 *
 */

cw_load('image');

//$available_images = cw_query_column("SELECT name FROM $tables[available_images]");
unset($available_images['magnifier_images']);
foreach ($available_images as $image_type=>$image_params) {
	
	$images_fs = array();
	$image_path = cw_image_dir($image_type);
	$_images_fs =  scandir($image_path);
	foreach ($_images_fs as $image) {
			if (is_file($image_path.'/'.$image))
				$images_fs[] = $image;
	}
	$images_db = cw_query_column("SELECT filename FROM $tables[$image_type] WHERE filename!='' AND image_path NOT LIKE 'http%'");
	$common = array_intersect($images_fs, $images_db);
	$in_db_only = array_diff($images_db, $images_fs);
	$in_fs_only = array_diff($images_fs, $images_db);

	$delete_fs = in_array($argv[3],array('--delete','--delete-fs'),true);
	$delete_db = in_array($argv[3],array('--delete','--delete-db'),true);	
	if ($delete_fs) {
		foreach($in_fs_only as $image) {
			if (is_file($image_path.'/'.$image))
				unlink($image_path.'/'.$image);
		}
	}
	if ($delete_db) {
		foreach($in_db_only as $image) {
			db_exec("DELETE FROM $tables[$image_type] WHERE filename=?", array($image));
		}
	}

    $images_db_http = cw_query_first_cell("SELECT count(*) FROM $tables[$image_type] WHERE image_path LIKE 'http%'");

	echo "[".str_pad($image_type,32,' ')."]\tHTTP:$images_db_http\tDB:".count($images_db)."\tFS:".count($images_fs)."\tcommon:".count($common).
	"\tLOST_IN_DB:".count($in_db_only)."\tLOST_IN_FS:".count($in_fs_only)."\t".($delete_db?'[DB CLEANED]':'').($delete_fs?'[FS CLEANED]':'')."\n";
	

	
}
