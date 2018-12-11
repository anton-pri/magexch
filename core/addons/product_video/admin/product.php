<?php
namespace cw\product_video;

if ($REQUEST_METHOD=='POST' && $js_tab != 'product_video') return;

$action_function = $action;

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
	$action_function = 'view';
}

// Call action
cw_call('cw\\'.addon_name.'\\'.$action_function, array($product_id));


if ($REQUEST_METHOD=='POST') {
    cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=$mode&product_id=$product_id&js_tab=product_video");
}

/* Actions */

function view($product_id) {
	global $smarty;
	$product_video = cw_call('cw\\'.addon_name.'\\get_product_video', array($product_id));
	$smarty->assign('product_video', $product_video);
	//cw_var_dump($product_video);
	return true;
}

function update_video($product_id) {
	global $tables;
	assert('!empty($product_id) /* '.__FUNCTION__.' */');
	
	$video = $_POST['video'];

	foreach ($video as $vid=>$v) {
		cw_array2update('product_video', $v, "video_id='$vid'", array('pos','title','descr','code'));
	}
	
	return true;	
}

function add_video($product_id) {
	global $new_video;
	assert('!empty($product_id) /* '.__FUNCTION__.' */');
	$new_video['product_id'] = $product_id;
	$new_video_id = cw_array2insert('product_video', $new_video, false, array('product_id','pos','title','descr','code'));
	if ($new_video_id) {
		cw_add_top_message('Video added');
	}
	return $new_video_id;
}

function delete_video($product_id) {
	global $tables;
	
	$video = $_POST['video'];
	$deleted = array();
	foreach ($video as $vid=>$v) {
		if ($v['delete']) {
			db_query("DELETE FROM $tables[product_video] WHERE video_id='$vid'");
			$deleted[] = $vid;
		}
	}
	
	return $deleted;
}
