<?php
global $config, $app_dir, $var_dirs_web, $smarty;

if (
	!empty($_GET['file'])
	&& trim($_GET['fkey'] === $config[feedback_addon_name]['fbr_secret_hash'])
) {
	$srcdir = $app_dir . '/files/' . feedback_files_folder_name . '/' . $_GET['file'];
	$srcwebdir = $var_dirs_web['files'] . '/' . feedback_files_folder_name . '/' . $_GET['file'];

	if (file_exists($srcdir . '/image.' . feedback_image_type)) {
		exit('<img src="' . $srcwebdir . '/image.' . feedback_image_type . '" alt="Feedback image" title="Feedback image" />');
	}
}

$top_message = &cw_session_register('top_message');
$top_message = array('type' => 'E', 'content' => 'The requested image does not exist.');

cw_header_location('index.php');
