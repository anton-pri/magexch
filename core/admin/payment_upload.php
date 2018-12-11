<?php
# kornev, TOFIX
if (!$addons['Salesman'])
    cw_header_location('index.php');

cw_load('files');

if ($action == 'upload') {
	$userfile = cw_move_uploaded_file("userfile");
	$fp = cw_fopen ($userfile, "r", true);
	if (!$fp)
		cw_header_location("index.php?target=error_message&error=cant_open_file");
	while ($columns = fgetcsv ($fp, 65536, $delimiter))
		db_query ("UPDATE $tables[salesman_payment] SET paid='".$columns[1]."', add_date='".time()."' WHERE doc_id='$columns[0]'");

	fclose ($fp);
	$top_message['content'] = cw_get_langvar_by_name("");
	$top_message['type'] = "W";
	cw_header_location("index.php?target=payment_upload");
} 
else {
	$smarty->assign('main', 'payment_upload');
}
