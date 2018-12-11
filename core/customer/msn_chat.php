<?php
require $app_main_dir."/include/msn/msn.class.php";

$msn_saved = &cw_session_register('msn_saved');
if ($action == "chat") {

    function spam() {
        global $msn, $msn_class, $config;
    }

    $msn_saved = $msn;
    if (!empty($msn['name']) && !empty($msn['password']) && !empty($msn['message'])) {
        $msn_class = new MezzengerKlient;
        $msn_class->debug=true;
        $msn_class->onLogin = "spam";
        $msn_class->init($msn['name'], $msn['password']);
        $msn_class->login();
        $msn_class->main();
        $msn_class->quit();
        $top_message['type'] = 'I';
        $top_message['content'] = cw_get_langvar_by_name('lbl_msn_message_send');
        $msn_saved = array();
    }
    else {
        $top_message['type'] = 'E';
        $top_message['content'] = cw_get_langvar_by_name('lbl_msn_please_fillin_fields');
    }
    cw_header_location('index.php?target=msn_chat');
}

$smarty->assign('msn', $msn_saved);
?>
