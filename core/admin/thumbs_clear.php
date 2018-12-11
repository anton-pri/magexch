<?php
# kornev, TOFIX
if (!isset($tables['cached_images'])) {
	$tables['cached_images'] = 'cw_xcm_cached_images';
}

$cache_files = cw_query("SELECT * FROM $tables[cached_images]");

if (empty($cache_files) || !is_array($cache_files)) {
	cw_header_location("index.php?target=settings&cat=Images");
}

cw_display_service_header();
cw_flush('Removing cached images: ...');

$cnt = 0;
if (count($cache_files)) {
	foreach($cache_files as $file) {
		@unlink($file['image_path']);
		$cnt++;
		if ($cnt > 5) {
			cw_flush('.');
		}
	}
}

db_query("TRUNCATE TABLE $tables[cached_images]");

cw_flush('<br />cached images are deleted.');

cw_header_location("index.php?target=settings&cat=Images");
exit;
