<?php
cw_load('ajax');

if (defined('IS_AJAX') && constant('IS_AJAX')) {
    global $config;

    if ($_GET['get_top_minicart']) {
        cw_add_ajax_block(array(
            'id' => 'microcart_content',
            'action' => 'update',
            'template' => 'addons/ajax_add2cart/top_minicart.tpl',
        ));
    }
    else {
        if ($config['Appearance']['place_where_display_minicart'] == 0) {
            // Update minicart
            cw_add_ajax_block(array(
                'id' => 'minicart',
                'action' => 'update',
                'template' => 'addons/ajax_add2cart/minicart.tpl',
            ));
        }

        cw_add_ajax_block(array(
            'id' => 'microcart',
            'action' => 'replace',
            'template' => 'customer/menu/microcart.tpl',
        ));
    }

    cw_event('on_minicart_update');
}
