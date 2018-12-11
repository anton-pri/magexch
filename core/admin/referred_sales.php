<?php
# kornev, TOFIX
if(!$addons['Salesman'])
    cw_header_location("index.php?target=error_message&error=access_denied&id=23");

include $app_main_dir."/include/referred_sales.php";

$smarty->assign('main', 'referred_sales');
