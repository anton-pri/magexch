<?php
function smarty_modifier_formatprice($price, $thousand_delim = NULL, $decimal_delim = NULL, $precision = NULL) {
	return cw_format_number($price, $thousand_delim, $decimal_delim, $precision);
}

?>
