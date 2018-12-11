<?php

define('SYSTEM_MESSAGE_INTERNAL', -1);
define('SYSTEM_MESSAGE_COMMON',    0);
define('SYSTEM_MESSAGE_AWAITING',  1);
define('SYSTEM_MESSAGE_SYSTEM',    2);

define('SYSTEM_MESSAGE_INFO',	 'I');
define('SYSTEM_MESSAGE_WARNING', 'W');
define('SYSTEM_MESSAGE_ERROR',	 'E');
define('SYSTEM_MESSAGE_CRITICAL','C');

function cw_system_messages_add($code, $msg, $type=SYSTEM_MESSAGE_COMMON, $severity=SYSTEM_MESSAGE_INFO) {
	global $tables;
	$code = db_escape_string($code);
	$msg = db_escape_string($msg);
	$type = intval($type);
	
	$existing = cw_query_first("SELECT code, hidden FROM $tables[system_messages] WHERE code='$code'");

	$data = array('date'=> cw_core_get_time(), 'message'=> $msg, 'type'=>$type, 'severity'=>$severity);
	if ($existing) {
		$ret = cw_array2update('system_messages', $data, "code='$code'");
	} else {
		$data['code'] = $code;
		$data['hidden'] = 0;
		$data['data'] = '';
		$ret = cw_array2insert('system_messages', $data);
	}
	
	return $ret;

}

function cw_system_messages_update_data($code, $data) {
    if (!is_scalar($data)) $data = serialize($data);

	$code = db_escape_string($code);
	$data = db_escape_string($data);
    
	$ret = cw_array2update('system_messages', array('data'=>$data), "code='$code'");

    return $ret;
}

function cw_system_messages_hide($code) {
	global $tables;
	$code = db_escape_string($code);

	return db_query("UPDATE $tables[system_messages] SET hidden='1' WHERE code='$code'");
}

function cw_system_messages_show($type) {
	global $tables;
	$type = intval($type);

	return db_query("UPDATE $tables[system_messages] SET hidden='0' WHERE type='$type'");
}

function cw_system_messages_delete($code) {
	global $tables;
	$code = db_escape_string($code);
	
	return db_query("DELETE FROM $tables[system_messages] WHERE code='$code'");
}

function cw_system_messages($type,$with_hidden=false) {
	global $tables;
	$type = intval($type);
	$sign = '=';
	if ($with_hidden) $sign = '>=';
	return cw_query_hash("SELECT sm.code as hash_key, sm.* FROM $tables[system_messages] sm WHERE sm.type='$type' AND sm.hidden $sign 0 
        ORDER BY (sm.severity = 'C') DESC, sm.date DESC", 'hash_key', false, false);
}

function cw_system_message($code) {
    global $tables;
    return cw_query_first("SELECT * FROM $tables[system_messages] WHERE code='".db_escape_string($code)."'");
}
