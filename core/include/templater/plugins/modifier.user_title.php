<?php
function smarty_modifier_user_title($value, $format = '{{firstname}} {{lastname}}', $doc_id = 0) {
    if ($format == 'P')
        return cw_warehouse_get_label($value);
    elseif($format == 'W')
        return cw_warehouse_get_title($value);
    elseif($format == 'S')
        return cw_user_get_label($value, '{{company}}', $doc_id);
    elseif($format == 'B')
        return cw_user_get_label($value, '{{firstname}} {{lastname}} {{customer_id}})', $doc_id);
    elseif($format == 'G')
        return cw_user_get_label($value, '{{firstname}} {{lastname}} (#{{customer_id}})', $doc_id);
    elseif($format == 'C')
        return cw_user_get_label($value, '{{firstname}} {{lastname}} / {{email}}', $doc_id);
    elseif($format == 'R')
        return cw_user_get_label($value, '{{company}} / {{email}}', $doc_id);
    return cw_user_get_label($value, '{{firstname}} {{lastname}}', $doc_id);
}
?>
