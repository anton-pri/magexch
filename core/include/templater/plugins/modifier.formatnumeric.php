<?php
function smarty_modifier_formatnumeric($price, $thousand_delim = NULL, $decimal_delim = NULL, $precision = NULL) {
	global $config;

	if (strlen(@$price) == 0)
		return $price;

	$format = $config['Appearance']['number_format'];

	if (empty($format)) $format = "2.,";

	if (is_null($thousand_delim) || $thousand_delim === false)
		$thousand_delim = substr($format,2,1);

	if (is_null($decimal_delim) || $decimal_delim === false)
		$decimal_delim = substr($format,1,1);

	if (is_null($precision) || $precision === false) {
		$price = (string)$price;
		$zero_pos = strpos($price, ".");
		$precision = ($zero_pos === false) ? 0 : (strlen($price)-$zero_pos-1);
	}

	return number_format((double)$price+0.00000000001, $precision, $decimal_delim, $thousand_delim);
}

?>
