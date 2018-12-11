<?php
function smarty_function_array_chunk($params, &$smarty) {

    if (!isset($params['var'])) {
        $smarty->trigger_error("array_chunk: missing 'var' parameter");
        return;
    }

    if(!is_array($params['var'])) {
//        $smarty->trigger_error("array_chunk: 'var' parameter should be array");
        return;
    }

    if(!isset($params['assign'])) {
        $smarty->trigger_error("array_chunk: missing 'assign' parameter");
        return;
    }

    if (empty($params['cols'])) $params['cols'] = 1;

    $smarty->assign($params['assign'], array_chunk($params['var'], $params['cols'], true));
}
?>
