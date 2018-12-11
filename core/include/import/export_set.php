<?php
cw_load('export');

// get objects count
if ($action == 'getcount') {
	$count = cw_objects_get_count();

	cw_add_ajax_block(array(
		'id' 		=> 'count_for_export',
		'action' 	=> 'update',
		'content' 	=> cw_get_langvar_by_name('add_to_export') . ' [' . $count . ']'
    ));

	cw_add_ajax_block(array(
		'id' 		=> 'widget_set_container',
		'action' 	=> 'update',
		'content' 	=> ($count ? $count . ' ' . cw_get_langvar_by_name('lbl_lc_products') . ' | <a href="javascript:resetSet();">' . cw_get_langvar_by_name('lbl_clear') . '</a>' : cw_get_langvar_by_name('lbl_all_products'))
    ));
}

// reset objects set
if ($action == 'reset') {
	$count = cw_objects_get_count();
	$result = cw_objects_reset();
	
	if ($result) {
		$count = 0;
	}

	cw_add_ajax_block(array(
		'id' 		=> 'count_for_export',
		'action' 	=> 'update',
		'content' 	=> cw_get_langvar_by_name('add_to_export') . ' [' . $count . ']'
    ));

	cw_add_ajax_block(array(
		'id' 		=> 'widget_set_container',
		'action' 	=> 'update',
		'content' 	=> ($count ? $count . ' ' . cw_get_langvar_by_name('lbl_lc_products') . ' | <a href="javascript:resetSet();">' . cw_get_langvar_by_name('lbl_clear') . '</a>' : cw_get_langvar_by_name('lbl_all_products'))
    ));
}

// add object to set
if ($action == 'add') {

	if (!empty($set_ids)) {
		$objects = explode(",", $set_ids);
		$result = cw_objects_add_to_set($objects);

		if ($result) {
			$count = cw_objects_get_count();
			
			cw_add_ajax_block(array(
				'id' 		=> 'count_for_export',
				'action' 	=> 'update',
				'content' 	=> cw_get_langvar_by_name('add_to_export') . ' [' . $count . ']'
		    ));
		    
		    cw_add_ajax_block(array(
				'id' 		=> 'widget_set_container',
				'action' 	=> 'update',
				'content' 	=> ($count ? $count . ' ' . cw_get_langvar_by_name('lbl_lc_products') . ' | <a href="javascript:resetSet();">' . cw_get_langvar_by_name('lbl_clear') . '</a>' : cw_get_langvar_by_name('lbl_all_products'))
		    ));
		}
	}
}
?>
