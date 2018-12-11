<?php
if (!defined('APP_START')) die('Access denied');

function cw_antibot_str_generator($length) {
	$str_num = "";

	for ($i = 0; $i < $length; $i++) {
		$number = rand(48, 90);
		if (($number > 57) && ($number < 65)) {
			$i--;
		} else {
			$str_num .= chr($number);
		}
	}
			    
	return $str_num;
}
?>
