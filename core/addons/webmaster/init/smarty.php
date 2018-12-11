<?php
namespace cw\webmaster;

$webmaster_status = cw_session_register('webmaster_status');

if (!empty($webmaster_status) && (APP_AREA != 'admin' || $config['webmaster']['webmaster_A'] == 'Y')) {
    $var_dirs['templates']  .= '_webmaster_'.$webmaster_status;
    $smarty->compile_dir = $var_dirs['templates'];
    $smarty->force_compile = true;
    cw_cache_clean('lang');
    define('WEBMASTER_STATUS', true);
    $smarty->assign('webmaster_status', true);
}

