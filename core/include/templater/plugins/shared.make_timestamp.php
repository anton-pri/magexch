<?php
function smarty_make_timestamp($string) {
	if(empty($string)) {
		$string = "now";
	}
	elseif (is_numeric($string)) {
		$time = (int)$string;
		if ($time >= 0) return $time;
	}

	$time = strtotime($string);
	if (is_numeric($time) && $time >= 0 && $time <= 2147472847)
		return $time;

	return time();
}
