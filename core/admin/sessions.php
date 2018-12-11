<?php
cw_include('include/security.php');
 
if ($action == 'kill') {
    db_query("delete from $tables[sessions_data] where sess_id='$sess_id'");
    cw_header_location("index.php?target=$target");
}
if ($action == 'details') {
	cw_load('dev');
	$sess = cw_query_first_cell("SELECT data FROM $tables[sessions_data] WHERE sess_id='$sess_id'");
	echo '<pre>';
	print_r(unserialize($sess));
	exit();
}
    
$time = cw_core_get_time();
$smarty->assign('current_time', $time);
$sessions = cw_query("select start, expiry, sd.customer_id, c.email, sess_id, ip from $tables[sessions_data] as sd left join $tables[customers] as c on c.customer_id = sd.customer_id order by start DESC");
$smarty->assign('sessions', $sessions);

$smarty->assign ('main', 'sessions');
?>
