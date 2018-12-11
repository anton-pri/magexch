<?php
function cw_instagram_on_logout() {
    $instagram_login_info = &cw_session_register('instagram_login_info');
    unset($instagram_login_info['data']);
    $instagram_login_info = array();
}
