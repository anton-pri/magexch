<?php
$smarty->assign('home_messages', cw_call('cw\news\get_messages',array($user_account['membership_id'], $current_language, false, 3)));

