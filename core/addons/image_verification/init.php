<?php
cw_include('addons/image_verification/include/cw.image_verification.php');

cw_addons_set_controllers(
    array('post', 'init/abstract.php', 'addons/image_verification/init/image_verification.php'),
    array('post', 'customer/acc_manager.php', 'addons/image_verification/antibot_err_display.php')
);

cw_event_listen('on_register_validate', 'cw_image_verification_on_register_validate');
