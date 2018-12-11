<?php
function smarty_block_jstabs($params, $content, &$smarty) {
    if (is_null($content)) {
        return;
    }
    
    $attrs = $params;
    
	if (isset($attrs['name']))
		$buffer = $attrs['name'];
	else
		$buffer = "js_tabs";

	if (isset($attrs['assign']))
		$assign = $attrs['assign'];
	else
		$assign = 'js_tabs';

	if (isset($attrs['default']))
		$selected = $attrs['default'];
	else
		$selected = 'js_tab';

	if (isset($attrs['buttons']))
		$buttons = $attrs['default'];
	else
		$buttons = 'js_tab_buttons';
		
	$_jstabs = $smarty->parse_ini_str($content, $buffer); 
	$smarty->assign($assign, $_jstabs['js_tabs']);
	$smarty->assign($selected, $_jstabs['selected']);
	$smarty->assign($buttons, $_jstabs['buttons']);

	return;
}
