<?php

if (APP_AREA == 'customer') {
    cw_set_controller('customer/index.php', 'addons/google_remarketing/customer/google_remarketing.php', EVENT_POST);
    cw_set_controller('customer/product.php', 'addons/google_remarketing/customer/google_remarketing.php', EVENT_POST);
    cw_set_controller('customer/cart.php', 'addons/google_remarketing/customer/google_remarketing.php', EVENT_POST);

    cw_addons_set_template(array('post', 'elements/bottom.tpl', 'addons/google_remarketing/google_remarketing.tpl'));
}