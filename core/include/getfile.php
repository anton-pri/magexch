<?php
cw_load( 'files', 'user');

$filename = $_GET['file'];

$file_exists = false;

#
# Check if file exists
#

$allowed_path = realpath(cw_user_get_files_location());

if (!@file_exists($filename)) {
	$filename = realpath($allowed_path.DIRECTORY_SEPARATOR.$filename);
	$file_exists = file_exists($filename);
}
else {
	$filename = realpath($filename);
	$file_exists = !strncmp($filename, $allowed_path, strlen($allowed_path));
}


if ($file_exists) {
#
# Output file content
#
	$imageinfo = cw_get_image_size($filename);
	if( !empty($imageinfo) )
		header("Content-type: ".($imageinfo[3]?$imageinfo[3]:"application/octet-stream"));
	else {
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=".basename($filename));
	}
	cw_readfile($filename);

}

exit;

?>
