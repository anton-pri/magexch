<?php
cw_load('product','attributes');

$product = cw_func_call('cw_product_get',array('id'=>$request_prepared['product_id'], 'info_type'=>0));

$attrs = cw_func_call('cw_attributes_get', array('item_id'=>$request_prepared['product_id'],'item_type'=>'P','attribute_fields'=>array('original_url')));

if ($product['product_type'] != PRODUCT_TYPE_CATALOG || empty($attrs['original_url']['value'])) {
	cw_header_location('index.php?target=product&product_id='.$request_prepared['product_id']);
}
	
// Increase add_to_cart counter	
cw_call_delayed('cw_product_run_counter', array('product_id' => $request_prepared['product_id'], 'count' => 1, 'type' => 3));

cw_header_location($attrs['original_url']['value']);
