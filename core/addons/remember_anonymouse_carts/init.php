<?php
if (!defined('APP_START')) die('Access denied');

if (!defined("RC_COOKIE_HISTORY"))      define ("RC_COOKIE_HISTORY",        "rcid");
if (!defined("RC_COOKIE_HISTORY_TEMP"))  define ("RC_COOKIE_HISTORY_TEMP",    "rcidtmp");
if (!defined("RC_COOKIE_START"))        define ("RC_COOKIE_START",          "rcstrt");
if (!defined("RC_DEBUG"))               define ("RC_DEBUG",                 "Y");


require $app_main_dir . '/addons/remember_anonymouse_carts/func.php';

if (APP_AREA == 'customer') 
{
    cw_addons_set_controllers(
        array('post', 'init/abstract.php', 'addons/remember_anonymouse_carts/abstract.php'),
        array('replace', 'customer/new_product.php', 'addons/remember_anonymouse_carts/new_product.php')
    );
    
    cw_addons_set_template(
        array('post', 'customer/menu/menu_sections.tpl', 'addons/remember_anonymouse_carts/line_js.tpl')
    );
}

    

