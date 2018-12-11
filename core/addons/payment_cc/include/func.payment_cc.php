<?php

function cw_payment_cc_run_processor($params, $return) {
    if ($params['payment_data']['processor'] == 'payment_cc') {
        return array('code' => 3);
    }
    return $return;
}

function cw_payment_cc_get_methods($params, $return) {
    if ($return['processor'] == 'payment_cc') {
        $return['ccinfo'] = true;
        $return['payment_type'] = 'cc';
    }
    return $return;
}
