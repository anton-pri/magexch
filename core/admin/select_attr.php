<?php 

cw_load('attributes');
$attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id'=>$_GET['attribute_id']));

//for selector we force sorting by value not by order id
if (function_exists('cw_attributes_default_values_selector_sort')) {
    if (is_array($attribute['default_value']))
        usort($attribute['default_value'], "cw_attributes_default_values_selector_sort");

    if (is_array($attribute['default_values']))
        usort($attribute['default_values'], "cw_attributes_default_values_selector_sort");
}

$attribute['value'] = $_GET['value'];
$attribute['values'] = array($_GET['value']);
$smarty->assign(array(
    'attribute'=> $attribute,
    'index' => empty($cd_id)?time():'cd_id'.str_replace('cd_id','',$cd_id),
//    'quantity' => $quantity,
    'value' => $_GET['value'],
    'operation' => $_GET['operation'],
    'no_extra_cmp' => $_GET['no_extra_cmp']
));
$smarty->assign('posted_name', $posted_name);

cw_ajax_add_block(array(
    'action' => 'append',
    'id' => 'select_attributes',
    'template' => 'main/select/attribute_row.tpl',
));
