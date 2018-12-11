<?php
/* vim: set ts=4 sw=4 sts=4 et: */

/**
 * Smarty {list2matrix} function plugin
 *
 * Type:     function
 * Name:     list2matrix
 * Purpose:  convert list to matrix
 * @param array parameters
 * @param Smarty
 * @return null
 */


function smarty_function_list2matrix($params, &$smarty)
{
    if (
        !isset($params['assign']) || !is_string($params['assign']) ||
        !isset($params['assign_width']) || !is_string($params['assign_width']) ||
        !isset($params['list']) || !is_array($params['list']) ||
        !isset($params['row_length'])
    ) {
        return;
    }

    $row_length = max(intval($params['row_length']), 1);

    $result = array();
    $i = 0;
    $n = 0;
    foreach ($params['list'] as $k => $v) {
        $i++;

        if (!isset($result[$n])) {
            $result[$n] = array();
        }

        $result[$n][$k] = $v;

        if ($i % $row_length == 0) {
            $n++;
        }
    }

    if (isset($params['full_matrix']) && $params['full_matrix'] && $i % $row_length != 0) {
        $end = $row_length - ($i % $row_length);
        for ($m = 0; $m < $end; $m++) {
            $result[$n][] = false;
        }
    }

    $smarty->assign_by_ref($params['assign'], $result);
    $smarty->assign($params['assign_width'], floor(100 / $row_length));

    return;
}

?>
