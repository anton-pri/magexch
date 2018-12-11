<?php
if (!defined('APP_START')) die('Access denied');

if ($action == 'bookmark') {
	if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[products_bookmarks] WHERE product_id='$product_id' AND customer_id='$customer_id'") == 0) {
		db_query ("INSERT INTO $tables[products_bookmarks] (product_id, customer_id, add_date) VALUES ('$product_id', '$customer_id', '".time()."')");
	}
} 
elseif ($action == 'delete_bookmark') {
	db_query ("DELETE FROM $tables[products_bookmarks] WHERE product_id='$product_id' AND customer_id='$customer_id'");
}

$bookmarks = cw_query ("SELECT $tables[products_bookmarks].product_id, $tables[products].product FROM $tables[products_bookmarks], $tables[products] WHERE $tables[products_bookmarks].product_id=$tables[products].product_id AND $tables[products_bookmarks].customer_id='$customer_id' ORDER BY $tables[products_bookmarks].add_date DESC");

$smarty->assign ('bookmarks', $bookmarks);
?>
