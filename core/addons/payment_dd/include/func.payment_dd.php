<?php

function cw_payment_dd_run_processor($params, $return) {
    if ($params['payment_data']['processor'] == 'payment_dd') {
        return array('code' => 3);
    }
    return $return;
}

function cw_payment_dd_get_methods($params, $return) {
    if ($return['processor'] == 'payment_dd') {
        $return['ccinfo'] = true;
        $return['payment_type'] = 'dd';
    }
    return $return;
}
