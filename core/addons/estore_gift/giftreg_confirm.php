<?php
#
# This function check the confirmation code
#
function cw_check_confirmation ($confirmation_code) {
	global $tables;
	
	if ($return = cw_query_first("SELECT reg_id, event_id, 'Y' as status FROM $tables[giftreg_maillist] WHERE MD5(CONCAT(confirmation_code,'_confirmed'))='$confirmation_code'"))
		return $return;
	elseif ($return = cw_query_first("SELECT reg_id, event_id, 'N' as status FROM $tables[giftreg_maillist] WHERE MD5(CONCAT(confirmation_code,'_declined'))='$confirmation_code'"))
		return $return;

	return false;
}

if (!empty($cc)) {
#
# Confirm/Decline the participation by recipient
#
	if ($conf_data = cw_check_confirmation($cc)) {

		db_query("UPDATE $tables[giftreg_maillist] SET status='$conf_data[status]', status_date='".time()."' WHERE reg_id='$conf_data[reg_id]'");

		if ($conf_data['status'] == "Y") {
			$access_status[$conf_data['event_id']] = "Y";
			$smarty->assign('message', 'confirmed');
		}
		elseif ($conf_data['status'] == "N")
			$smarty->assign('message', 'declined');

		$eventid = $conf_data['event_id'];
	}
	else
		cw_header_location("index.php");

}
?>
