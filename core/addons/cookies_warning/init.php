<?php

cw_addons_add_css('addons/cookies_warning/customer/cookie_warn.css');
cw_addons_set_template(
    array('pre', 'customer/head.tpl', 'addons/cookies_warning/customer/top_panel.tpl')
);

cw_addons_set_controllers(
    array('post', 'customer/auth.php', 'addons/cookies_warning/customer/auth.php')
);

cw_addons_set_controllers(
    array('pre', 'admin/auth.php', 'addons/cookies_warning/common/auth.php'),
    array('pre', 'seller/auth.php', 'addons/cookies_warning/common/auth.php')
);
