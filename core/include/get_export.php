<?php
$file = basename($file);
$filename = $var_dirs['tmp'].DIRECTORY_SEPARATOR.$file.".php";

$fn = str_replace(".php", "", $file);
header('Content-type: text/csv; name="'.$fn.'"');
header("Content-Disposition: attachment; filename=".$fn);
header('Content-length: '.(@filesize($filename)-strlen(LOG_PHP_SIGNATURE)));

$fp = @fopen($filename, "rb");
if ($fp !== false) {
	fseek($fp, strlen(LOG_PHP_SIGNATURE), SEEK_SET);
	fpassthru($fp);
	fclose($fp);
}

?>
