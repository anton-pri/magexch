<?php
/*
print_r($_GET);
print("stage_lib_id: $stage_lib_id");
*/

$stage_setting = cw_query_first($s = "select * from $tables[product_stages_library] ps where ps.stage_lib_id='$stage_lib_id'");

if (empty($stage_setting)) {
    print("Error: #$stage_lib_id is incorrect product stage_lib_id!");
    die;
} else {
    $smarty->assign('stage_message_subject', $stage_setting['subject']);
    $smarty->assign('stage_message_body', $stage_setting['body']);
}

$smarty->assign('main', 'stage_email_test');
$smarty->assign('home_style', 'iframe');
define('PREVENT_XML_OUT', true);
