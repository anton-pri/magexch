<?php
if (!defined('APP_START')) die('Access denied');

#
# Assign 6-digits interneka ID to smarty (it's need for affiliates sign-up menu)
#
$smarty->assign('interneka_id6', sprintf("%06d", $config['interneka']['interneka_id']));

?>
