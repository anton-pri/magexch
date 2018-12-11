<?php
function smarty_function_count($params, &$smarty) {
    if (!isset($params['value'])) {
        if (isset($params['assign'])) {
            $smarty->assign($params['assign'], 0);
            return;
        }
        return 0;
    }

    $len = (is_array($params['value']) ? count($params['value']) : strlen($params['value']));
    $len = intval($len);

    if (isset($params['assign'])) {
        $smarty->assign($params['assign'], $len);
        return;
    }

    if (isset($params['print']) && !$params['print'])
        $len = null;

    return $len;
}

?>
