<?php
function smarty_modifier_attribute_data($value) {
	cw_load('attributes');

	$data = "";
	$use_description = &cw_session_register('use_description', true);

	if (preg_match('/(\w+)\.name/', $value, $matches)) {
		// Get attribute name by field
		$field_name = $matches[1];
		if (!empty($field_name)) {
			$attribute_id = cw_attributes_get_attribute_by_field($field_name);
			$attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id' => $attribute_id));
			$data = $attribute['name'];
		}
	} else if (preg_match('/(\w+)\.value/', $value, $matches)) {
		// Get attribute value by field
        global $product_filter;
		$pf = &$product_filter;
		$field_name = $matches[1];
		if ($pf && $field_name) {
			foreach ($pf as $pf_value) {
				if ($pf_value['field'] == $field_name) {
					if ($pf_value['selected']) {
						foreach ($pf_value['selected'] as $pfs_value) {
							if (isset($pf_value['values'][$pfs_value])) {
								$data = $pf_value['values'][$pfs_value]['name'];
							}
						}
					}
				}
			}
		}
	} else if ($use_description && preg_match('/(\w+)\.description/', $value, $matches)) {
		// Get attribute value by field
		$field_name = $matches[1];
		if (!empty($field_name)) {
			global $smarty;

			$attribute_id = cw_attributes_get_attribute_by_field($field_name);
			$attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id' => $attribute_id));

			$use_description = false;
			require_once($smarty->_get_plugin_filepath('function', 'eval'));
			$data = smarty_function_eval(array('var' => $attribute['description']), $smarty);
			$use_description = true;
		}
	}

    return $data;
}
