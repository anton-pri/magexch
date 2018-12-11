<?php
cw_include('addons/' . messaging_addon_name . '/include/message_box.php');

// get counter messages in folders for message_box
$messages_counter = cw_messages_get_messages_counters($customer_id);
$smarty->assign('messages_counter', $messages_counter);
