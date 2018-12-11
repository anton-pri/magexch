<?php
cw_load('salesman_orders');
$smarty->assign('salesman_orders', cw_get_salesman_pending_orders());
$smarty->assign('main', 'salesman_created_orders');
