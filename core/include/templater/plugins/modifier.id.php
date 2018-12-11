<?php
function smarty_modifier_id($value) {
	return strtolower(preg_replace('/[^\w\d_]/', '', $value));
}
?>
