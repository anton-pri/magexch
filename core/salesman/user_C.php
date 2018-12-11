<?php
if (in_array($action, array('update', 'delete', 'update_address'))) $action = '';
$usertype = 'C';

$search_data = &cw_session_register('search_data', array());
$search_data['users'][$usertype]['sale']['sales_manager'] = array($customer_id => 1);
include $app_main_dir.'/include/users/info.php';
