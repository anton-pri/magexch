<?php
function smarty_modifier_trademark($value, $flag="", $alt="") {
	$reg = $sm = $tm = "";

	if (!empty($flag)) {
		$reg = "&#174;";
		$sm = "<sup>SM</sup>";
		$tm = "<sup>TM</sup>";
	}

	if (!empty($alt)) {
		$sm = " (SM)";
		$tm = " (TM)";
	}

	$result = preg_replace("/##R##/", $reg, $value);
	$result = preg_replace("/##SM##/", $sm, $result);
	$result = preg_replace("/##TM##/", $tm, $result);

	return $result;
}

?>
