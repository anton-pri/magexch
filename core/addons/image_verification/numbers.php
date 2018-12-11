<?php
if (!defined('APP_START')) die("Access denied");

function cw_antibot_str_generator($length) {
	$str_num = "";

	for ($i = 0; $i < $length; $i++) {
		$str_num .= rand(1, 9);
	}
	return $str_num;
}
?>
