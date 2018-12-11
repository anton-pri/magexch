<?php

define('DOD_OBJ_TYPE_PRODS',     1);
define('DOD_OBJ_TYPE_CATS',      2);
define('DOD_OBJ_TYPE_MANS',      3);
define('DOD_OBJ_TYPE_ATTR',      9);
define('DOD_OBJ_TYPE_SHIPPING', 10);

define('DOD_APPLY_PRODS',    3);

define('DOD_DISCOUNT',   'D');
define('DOD_FREE_PRODS', 'F');
define('DOD_FREE_SHIP',  'S');
define('DOD_COUPON',     'C');

define('DOD_ATTR_ITEM_TYPE', 'DD');

$bonus_names = array(
    DOD_COUPON     => 'lbl_dod_bonus_coupon',
    DOD_DISCOUNT   => 'lbl_dod_bonus_discount',
    DOD_FREE_PRODS => 'lbl_dod_bonus_forfree',
    DOD_FREE_SHIP  => 'lbl_dod_bonus_freeship'
);

/* dod tables */
$_addon_tables = array('dod_generators', 'dod_bonuses', 'dod_bonus_details');
foreach ($_addon_tables as $_table) {
    $tables[$_table] = 'cw_' . $_table;
}

cw_include('addons/deal_of_day/include/func.php');

if (APP_AREA == 'admin') {

    cw_addons_set_controllers(
        array('replace', 'admin/deal_of_day.php', 'addons/deal_of_day/admin/deal_of_day.php'),
        array('post', 'include/auth.php', 'addons/deal_of_day/include/auth.php')
    );

    cw_addons_set_template(
        array('replace', 'admin/main/deal_of_day.tpl', 'addons/deal_of_day/admin/main.tpl')
    );

    cw_addons_add_css('addons/deal_of_day/admin/deal_of_day.css');
}

if (APP_AREA == 'customer') {
    cw_addons_set_controllers(
        array('replace', 'customer/deal_of_day_generate.php', 'addons/deal_of_day/customer/deal_of_day_generate.php'),
        array('post', 'customer/index.php', 'addons/deal_of_day/customer/index.php')
    );

    cw_addons_set_template(
        array('post', 'customer/main/welcome.tpl@home_offers','addons/deal_of_day/customer/home.tpl')
    );

}
