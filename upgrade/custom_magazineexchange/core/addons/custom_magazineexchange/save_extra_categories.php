<?php

if ($REQUEST_METHOD == "POST" && $action == 'move' && isset($cat_parents) && !empty($cat)) {
    db_query("delete from $tables[categories_extra] where category_id='$cat'");
    foreach($cat_parents as $_parent_id) {
        if (!empty($_parent_id))
            cw_array2insert("categories_extra", array('category_id'=>$cat, 'parent_id'=>$_parent_id));
    }
}
