<?php
if ($customer_id) {
    $smarty->assign('main', 'main');
} else {
    $smarty->assign('main', 'welcome');
}
