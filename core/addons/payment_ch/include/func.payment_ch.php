<?php

function cw_payment_ch_run_processor($params, $return) {
    if ($params['payment_data']['processor'] == 'payment_ch') {
        return array('code' => 3);
    }
    return $return;
}

function cw_payment_ch_get_methods($params, $return) {
    if ($return['processor'] == 'payment_ch') {
        $return['ccinfo'] = true;
        $return['payment_type'] = 'ch';
    }
    return $return;
}
