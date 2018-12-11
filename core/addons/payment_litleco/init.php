<?php
/*
 * Vendor: CW
 * addon: Litleco protocol
 */
const litleco_addon_name           = 'payment_litleco';
const litleco_addon_target         = 'litleco';
const litleco_addon_version        = '0.1';

if (!empty($addons[litleco_addon_name])) {
    cw_include('addons/' . litleco_addon_name . '/include/func.litleco.php');

    cw_addons_set_hooks(
        array('post', 'cw_payment_get_methods', 'cw_payment_litleco_get_methods'),
        array('post', 'cw_payment_run_processor', 'cw_payment_litleco_run_processor')
    );
}
