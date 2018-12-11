<?php
$items_per_page_targets = &cw_session_register('items_per_page_targets', array());
if (
	!empty($_GET['items_per_page']) 
	|| !empty($items_per_page) 
	|| !empty($_GET['view_all']) 
	|| !empty($view_all)
) {
	if (
		(!empty($_GET['view_all']) || !empty($view_all)) 
		&& !empty($app_config_file['interface']['max_count_view_products'])
		&& $current_area == 'C'
	) {
		$items_per_page_targets[$target] = $app_config_file['interface']['max_count_view_products'];
	}
	else {
    	$items_per_page_targets[$target] = empty($_GET['items_per_page']) ? intval($items_per_page) : intval($_GET['items_per_page']);
	}
	
    cw_core_save_navigation($customer_id, $items_per_page_targets);
}
