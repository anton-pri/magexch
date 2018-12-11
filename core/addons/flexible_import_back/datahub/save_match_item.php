<?php
$buffer_match_pre_save = &cw_session_register('buffer_match_pre_save', array());

//if (isset($buffer_match_pre_save[$_GET['table_id']])) 
//    $buffer_match_pre_save_prev_value = $buffer_match_pre_save[$_GET['table_id']];

$buffer_match_pre_save[$_GET['table_id']] = $_GET['key'];

$manual_sel_items = &cw_session_register('manual_sel_items', array());

$manual_sel_k = 9999999;

if ($_GET['key'] == $manual_sel_k) {
    if ($_GET['manual_sel_id']) 
        $manual_sel_items[$_GET['table_id']] = $_GET['manual_sel_id'];
/*
    elseif (isset($buffer_match_pre_save_prev_value)) 
        $buffer_match_pre_save[$_GET['table_id']] = $buffer_match_pre_save_prev_value;
*/
}

exit;
