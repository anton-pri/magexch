<?php
function smarty_modifier_comma_format($value) {
	return cw_format_number($value, null, ',', null);
}
?>
