<?php

$interim_ext = '';
if ($_GET['is_interim'] == 1)
    $interim_ext = 'interim_';

$buffer_match_pre_save = cw_dh_session_register($interim_ext.'buffer_match_pre_save', array());

//if (isset($buffer_match_pre_save[$_GET['table_id']])) 
//    $buffer_match_pre_save_prev_value = $buffer_match_pre_save[$_GET['table_id']];

$buffer_match_pre_save[$_GET['table_id']] = $_GET['key'];

$manual_sel_items = cw_dh_session_register($interim_ext.'manual_sel_items', array());

$manual_sel_k = 9999999;

if ($_GET['key'] == $manual_sel_k) {
    if ($_GET['manual_sel_id']) 
        $manual_sel_items[$_GET['table_id']] = $_GET['manual_sel_id'];
/*
    elseif (isset($buffer_match_pre_save_prev_value)) 
        $buffer_match_pre_save[$_GET['table_id']] = $buffer_match_pre_save_prev_value;
*/
}

cw_dh_session_save($interim_ext.'buffer_match_pre_save', $buffer_match_pre_save);
cw_dh_session_save($interim_ext.'manual_sel_items', $manual_sel_items);

exit;
