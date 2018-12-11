<?php
cw_load('user');
$custom_admin_fields = cw_user_get_custom_fields($customer_id,0,'','field');
if ($custom_admin_fields['only_inline_edit'] == 'Y') { 
    global $APP_SESS_ID;
    $webmaster_status =& cw_session_register('webmaster_status');
    $webmaster_status = $APP_SESS_ID; 

    cw_header_location('../index.php');
}
