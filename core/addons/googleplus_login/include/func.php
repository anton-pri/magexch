<?php
function cw_googleplus_on_logout() {
    $googleplus_login_info = &cw_session_register('googleplus_login_info');
    unset($googleplus_login_info['token']);

    $google_client_id       = '376787991969-2c127o3n2vollhqfla26q1mfu1qi7n8s.apps.googleusercontent.com';
    $google_client_secret   = '25mdbO_DAlPE_aST_hErSzDN';
    $google_redirect_url    = 'http://dev.cartworks.com/product_stages/index.php'; //path to your script
    $google_developer_key   = 'AIzaSyAOCvjaVfFFiL4OnlI8du8pHHNZGPsY3iU';

    cw_include('addons/googleplus_login/include/src/Google_Client.php');
    cw_include('addons/googleplus_login/include/src/contrib/Google_Oauth2Service.php');

    $gClient = new Google_Client();
    $gClient->setApplicationName('Test Google+ Login CW');
    $gClient->setClientId($google_client_id);
    $gClient->setClientSecret($google_client_secret);
    $gClient->setRedirectUri($google_redirect_url);
    $gClient->setDeveloperKey($google_developer_key);

    $gClient->revokeToken();
}
