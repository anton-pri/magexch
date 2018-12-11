<?php
/* vim: set ts=4 sw=4 sts=4 et: */

/**
 * Templater plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     interline
 * Input:    class
 *           total
 *           index
 *            additional_class
 * -------------------------------------------------------------
 */

function smarty_function_interline($params, &$smarty)
{
    if (
        (
            isset($params['class']) 
            && (
                !is_string($params['class']) 
                || empty($params['class'])
            )
        ) || (
            (
                !isset($params['name']) 
                || !is_string($params['name']) 
                || empty($params['name']) 
                || !isset($smarty->_foreach[$params['name']])
            ) && (
                !isset($params['total']) 
                || !is_int($params['total']) 
                || $params['total'] < 1 
                || !isset($params['index']) 
                || !is_int($params['index']) 
                || $params['index'] < 0 
                || $params['index'] > $params['total'] - 1
            )
        )
    ) {
        return '';
    }

    if (isset($params['name'])) {
        $params['total'] = $smarty->_foreach[$params['name']]['total'];
        $params['index'] = max(0, $smarty->_foreach[$params['name']]['iteration'] - 1);
    }

    if (!isset($params['class']) && empty($params['skip_highlight']))
        $params['class'] = 'highlight';

    $class = array();

    if ($params['total'] % 2 == ($params['index'] + 1) % 2)
        $class[] = $params['class'];

    if ($params['index'] == 0)
        $class[] = 'first';

    if ($params['index'] >= $params['total'] - 1)
        $class[] = 'last';

    if (!empty($params['additional_class']))
        $class[] = $params['additional_class'];

    $class = implode(" ", $class);

    if (!empty($class) && (!isset($params['pure']) || !$params['pure']))
        $class = ' class="' . $class . '"';

    if (isset($params['assign'])) {
        $smarty->assign($params['assign'], $class);
        $class = '';
    }

    return $class;
}

?>
