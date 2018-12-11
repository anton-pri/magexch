<?php
$smarty->assign('number_format_dec', $config['Appearance']['number_format']{1});
$smarty->assign('number_format_th', $config['Appearance']['number_format']{2});
$smarty->assign('number_format_point', intval($config['Appearance']['number_format']{0}));

$smarty->assign('zero', cw_format_number(0));
