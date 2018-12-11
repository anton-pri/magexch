<?php
namespace CW\bookmarks;

function get_by_customer($customer_id) {
    global $tables;
    if (intval($customer_id) == 0) return null;
    return cw_query("SELECT * FROM $tables[bookmarks] WHERE customer_id='".intval($customer_id)."'");
}

function get_by_session($sess_id) {
    global $tables;
    return cw_query("SELECT * FROM $tables[bookmarks] WHERE sess_id='$sess_id'");
}

function on_customer_delete($customer_id) {
    global $tables;
    return db_query("DELETE FROM $tables[bookmarks] WHERE customer_id='".intval($customer_id)."'");
}

function on_sessions_delete($sess_id) {
    global $tables;

	// delete bookmarks of expired non-registered sessions
    $sess_ids = cw_query_column("SELECT b.sess_id 
		FROM $tables[bookmarks] b 
		LEFT JOIN $tables[sessions_data] sd ON sd.sess_id = LEFT( b.sess_id, LENGTH( sd.sess_id ))  
		WHERE sd.sess_id IS NULL and b.customer_id=0");
    if ($sess_ids)
		foreach($sess_ids as $sess_id)
			db_query("delete from $tables[bookmarks] where sess_id='$sess_id'");
}

function on_login($customer_id, $area, $on_register=0) {
    global $tables, $APP_SESS_ID;
    return db_query("UPDATE $tables[bookmarks] SET customer_id='".intval($customer_id)."' WHERE customer_id=0 AND sess_id='$APP_SESS_ID-$area'");
}
