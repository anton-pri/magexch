<?php

function cw_payment_offline_run_processor($params, $return) {
    if ($params['payment_data']['processor'] == 'payment_offline') {
        return array('code' => 3);
    }
    return $return;
}
