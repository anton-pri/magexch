<?php
#
# This addon generates download key which is sent to customer
# and inserts this key into database
#
function keygen($product_id, $key_TTL, $item_id) {
	global $tables;

	$key = md5(uniqid(rand()));
	$expires = time() + $key_TTL*3600;

	db_query("REPLACE INTO $tables[download_keys] (download_key, expires, product_id, item_id) VALUES('$key', '$expires', '$product_id', '$item_id')");
	return $key;
}

?>
