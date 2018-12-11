<?php
cw_lock("antibot");

$sess_id = cw_session_id();
cw_session_start($sess_id);
if ($addons['image_verification']) {
	require_once $app_main_dir."/addons/image_verification/antibot_image.php";
}
function unlock_antibot() {
	cw_unlock("antibot");
}

register_shutdown_function('unlock_antibot');
?>
