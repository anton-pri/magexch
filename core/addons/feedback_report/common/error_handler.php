<?php
global $config;

// custom error handler
if ($config[feedback_addon_name]['fbr_create_feedback_on_error'] == 'Y') {
	register_shutdown_function("cw_fbr_error_handler");
}
