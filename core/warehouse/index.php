<?php
if ($customer_id) {
    cw_load('tabs');
    $smarty->assign('main', 'main');
}
else
    $smarty->assign('main', 'welcome');

