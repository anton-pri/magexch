<?php
/* Area specific handlers and pre-hooks go here that fills ajax_blocks variable using cw_add_ajax_block() */
/**
  cw_add_ajax_block(array(
    'id' => <div_id>,
    'action' => update|remove|show|hide|append|prepend|after|before,
    'content' => <html_content>,
    ['template' => <template_path>,]
    ));
*/

global $target, $ajax_blocks, $area, $mode, $is_ajax;


/* TODO: rework AJAX to use XML */
// Old style AJAX JSON requests
if ($mode == 'barcode') {
    cw_load('barcode');
    cw_barcode_get($barcode, $type, $width, $height);
    exit();
}
if ($mode == 'categories')
    cw_include('include/ajax/categories.php');
if (in_array($mode, array('counties', 'states', 'regions', 'cities')))
    cw_include('include/map/ajax_countries.php');
if ($mode == 'aom')
    cw_include('include/orders/order_edit_ajax.php');
// < Old style AJAX JSON requests

if ($mode == 'map') {
/* mode "map" acceptes following parameters
 country - country code
 state - state code
 name - name of fields, which will be extended by [state] and [country]
*/
    cw_include('include/map/ajax_map.php');
}

if (!empty($top_message)) {
    $top_message = array();
}

$smarty->assign('is_ajax', $is_ajax);
$smarty->assign('ajax_blocks',$ajax_blocks);
// XML AJAX response
header('Content-type: text/xml');
cw_display('main/ajax/ajax_response.tpl',$smarty);
exit(0);
