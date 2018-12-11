<?php
function smarty_modifier_abs_value($value) {
	if (is_numeric($value))
		return abs($value);
	return $value;
}
?>
