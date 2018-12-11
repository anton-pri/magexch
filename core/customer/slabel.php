<?php
require "./top.inc.php";
require "./init.php";

if (empty($doc_id))
	exit;

$login_type = &cw_session_register("login_type");

$file_content = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id = '$doc_id' AND khash = 'shipping_label'");
$file_type = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id = '$doc_id' AND khash = 'shipping_label_type'");

if ($file_type == 'error' || empty($file_content) || $login_type != 'A' && $login_type != 'P') {
	cw_header_location("index.php?target=error_message&error=access_denied&id=72");
}

header("Content-Type: ".$file_type);
header("Content-Disposition: attachment; filename=\"label.".preg_replace("/^\w+\//i", "", $file_type)."\"");
header("Content-Length: ".strlen($file_content));

echo $file_content;

exit;

?>
