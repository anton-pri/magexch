<?php

if (APP_AREA == 'customer') {
    cw_set_controller('customer/ajax_product_search.php', 'addons/ajax_search/customer/ajax_search.php', EVENT_REPLACE);

    cw_addons_set_template(array('post', 'js/presets_js.tpl', 'addons/ajax_search/ajax_search.tpl'));

    cw_addons_add_js('addons/ajax_search/ajax_search.js');
    cw_addons_add_css('addons/ajax_search/ajax_search.css');
}