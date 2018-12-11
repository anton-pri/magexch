<?php

cw_addons_set_controllers(
    array('post', 'customer/auth.php', 'addons/salesman/include/salesman_info.php'),
    array('post', 'payment/auth.php', 'addons/salesman/include/salesman_info.php'),
    array('post', 'customer/auth.php', 'addons/salesman/include/adv_info.php'),
    array('post', 'payment/auth.php', 'addons/salesman/include/adv_info.php')
);

$tables['salesman_orders'] = 'cw_salesman_orders';
$tables['salesman_adv_campaigns'] = 'cw_salesman_adv_campaigns';
$tables['salesman_adv_clicks'] = 'cw_salesman_adv_clicks';
$tables['salesman_adv_orders'] = 'cw_salesman_adv_orders';
$tables['salesman_banners'] = 'cw_salesman_banners';
$tables['salesman_banners_elements'] = 'cw_salesman_banners_elements';
$tables['salesman_clicks'] = 'cw_salesman_clicks';
$tables['salesman_commissions'] = 'cw_salesman_commissions';
$tables['salesman_payment'] = 'cw_salesman_payment';
$tables['salesman_plans'] = 'cw_salesman_plans';
$tables['salesman_plans_commissions'] = 'cw_salesman_plans_commissions';
$tables['salesman_product_commissions'] = 'cw_salesman_product_commissions';
$tables['salesman_commissions'] = 'cw_salesman_commissions';
$tables['salesman_tier_commissions'] = 'cw_salesman_tier_commissions';
$tables['salesman_views'] = 'cw_salesman_views';
$tables['salesman_premiums'] = 'cw_salesman_premiums';
$tables['salesman_premiums_lng'] = 'cw_salesman_premiums_lng';
$tables['salesman_target'] = 'cw_salesman_target';
?>
