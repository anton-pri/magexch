<?php
function smarty_modifier_amp($value) {
	return preg_replace("/&(?!amp;)/S", "&amp;", $value);
}

?>
