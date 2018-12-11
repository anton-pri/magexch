<?php
/**
 * Templater plugin
 *
 * Collects all called images, groups them by group parameter and output <div /> tag with assigned class
 * 
 * @param array params
 * 		src 	- link to image like in img tag
 *		group 	- name of used sprite
 *		id 		- identifier attribute for tag
 *		name 	- name attribute for tag
 *		class	- to add additional class to resulted div
 * 		title	- add popup title
 */

function smarty_function_image_sprite($params, &$smarty) {

	if (!isset($params['src']) || empty($params['src'])) {
		$smarty->trigger_error("assign: missing 'src' parameter");
		return;
	}

	if (!isset($params['group']) || empty($params['group'])) {
		$smarty->trigger_error("assign: missing 'group' parameter");
		return;
	}

	$smarty->_smarty_vars['sprites'][$params['group']][] = $params['src'];
	
	$add_class	= !empty($params['class']) 	? ' ' . $params['class'] 				: '';
	$ident		= !empty($params['id']) 	? ' id="' . $params['id'] . '"' 		: '';
	$name		= !empty($params['name']) 	? ' name="' . $params['name'] . '"' 	: '';
	$title		= !empty($params['title']) 	? ' title="' . $params['title'] . '"' 	: '';
	
	// classname based on constants qpimg::MAP_CSS_SELECTOR_MASK and qpimg::OBJ_CSS_SELECTOR_MASK
	// can change in config, use params map_css_selector_mask and obj_css_selector_mask
	// if change in config, need change string below and function cw_generate_css_sprites !!! 
	$class_name = "qpimg_map_" . $params['group'] . " qpimg_obj_" . $params['group'] . "_sprite" . count($smarty->_smarty_vars['sprites'][$params['group']]);
	
	return '<div class="' . $class_name . $add_class . '" ' . $ident . $name . $title . '></div>';
}

?>
