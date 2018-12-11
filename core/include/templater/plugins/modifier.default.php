<?php
function smarty_modifier_default($value, $default = '') {
	return empty($value) ? $default : $value;
}
?>
