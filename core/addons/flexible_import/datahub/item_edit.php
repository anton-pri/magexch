<?php

//$test_ext = '_test2';

$allowed_tables = array('item_xref'.$test_ext, 'item_store2'.$test_ext);

if (in_array($table_name, $allowed_tables)) {
    $allowed_fields = cw_check_field_names(array(), $table_name);
    if (in_array($key_field, $allowed_fields) && in_array($key_field, array('item_id', 'store_sku', 'xref'))) {
        $data2edit = cw_query("SELECT * FROM $table_name WHERE `$key_field`='$key_value' LIMIT 100");
        if (!empty($data2edit)) {
            $smarty->assign('data2edit', $data2edit);
            if ($action == 'delete') {
                $del_query = "DELETE FROM $table_name WHERE `$key_field`='$key_value'";
                //e($del_query); 
                db_query($del_query);
                cw_add_top_message("The hub item $key_field=$key_value has been deleted from $table_name",'I');
                cw_header_location("index.php?target=datahub_item_edit&table_name=$table_name&key_field=$key_field&key_value=$key_value");  
            }
        }


        $smarty->assign('table_name', $table_name);
        $smarty->assign('key_field', $key_field);
        $smarty->assign('key_value', $key_value);
    }
}

$smarty->assign('main', 'datahub_item_edit');
