<?php
function smarty_function_get_column_counter($params, &$smarty) {
    global $columns_counter;

    if (!isset($params['usertype'])) {
        $smarty->trigger_error("eval: missing 'usertype' parameter");
        return;
    }
    $columns_counter[$params['usertype']]++;
    
    $smarty->assign('columns_counter', $columns_counter);

    return $columns_counter[$params['usertype']];
}

/* vim: set expandtab: */

?>
