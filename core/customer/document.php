<?php
$identifiers = &cw_session_register('identifiers', array());

cw_load('file_area');
$file_info = cw_file_get_doc($type, $file_id);

if (!$identifiers['A'] && $file_info['customer_id'] != $customer_id) cw_header_location('index.php');

if (!$file_info || !is_file($file_info['file_path'])) cw_header_location('index.php');

header("Cache-Control: no-cache\n"); 
header("Content-disposition: attachment; filename=".$file_info['filename']); 
header("Content-type: application/download");
header("Pragma: no-cache");
header("Expires: 0"); 

echo file_get_contents($file_info['file_path']);
die;
?>
