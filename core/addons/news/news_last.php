<?php
if (!defined('IS_AJAX'))
    $smarty->assign('news_message', cw_call('cw\news\get_messages',array($user_account['membership_id'], $current_language, true)));

