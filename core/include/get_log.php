<?php
$logs = cw_log_list_files();
$is_found = false;

if (!empty($logs)) {
	foreach ($logs as $arr) {
		if (in_array($file, $arr)) {
			$is_found = true;
			break;
		}
	}
}

$filename = $var_dirs['log'].DIRECTORY_SEPARATOR.$file;

$fp = fopen($filename, "rb");
if (!$is_found || empty($file) || !$fp) {
	cw_header_location("index.php?target=error_message&error=access_denied&id=62");
}

if ($action == 'delete') {
    fclose($fp);
    @unlink($filename);
    cw_add_top_message("The log file $file has been deleted", 'I');
    cw_header_location($_SERVER['HTTP_REFERER']);
    exit;
}

header('Content-Type: text/plain; name="'.preg_replace("/\.php$/", ".txt", $file).'"');
header('Content-Length: '.(filesize($filename)-strlen(LOG_PHP_SIGNATURE)));

fseek($fp, strlen(LOG_PHP_SIGNATURE), SEEK_SET);
fpassthru($fp);
fclose($fp);
exit;
?>
