<?php
global $customer_id;

if (!empty($fg_target) && $customer_id) {

    $is_hidden = cw_query_first_cell("select count(*) from $tables[admin_forms_hidedisplay] where customer_id = '$customer_id' and target = '$fg_target' and mode = '$fg_mode' and element_name = '$fg_idx'");

    if ($is_hidden) {
        db_query("delete from $tables[admin_forms_hidedisplay] where customer_id = '$customer_id' and target = '$fg_target' and mode = '$fg_mode' and element_name = '$fg_idx'");
        cw_add_ajax_block(array(
            'id' => 'script',
            'content' => "fa_toggle_element('$fg_id','$fg_idx', 0); fa_eye_show_all_btn(0);",
        ));

    } else {

        cw_array2insert('admin_forms_hidedisplay', 
            array('customer_id' => $customer_id, 'target' => $fg_target, 'mode' => $fg_mode, 'element_name' => $fg_idx));

        cw_add_ajax_block(array(
            'id' => 'script',
            'content' => "fa_toggle_element('$fg_id','$fg_idx', 1); fa_eye_show_all_btn(1);",
        ));
    }
}

