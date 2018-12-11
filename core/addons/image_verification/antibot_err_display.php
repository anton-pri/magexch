<?php

$antibot_err = &cw_session_register("antibot_err");
if ($addons['image_verification'] && $antibot_err) {
    $smarty->assign_by_ref('antibot_err', $antibot_err);
    cw_session_unregister("antibot_err");
}

