<?php
// Service tool for admin

/*
 * E-Mail
 */

if ($mode == 'email') {

    cw_load('mail');

    if ($action == 'clean_spool') {
        db_query("DELETE FROM $tables[mail_spool]");
    }

    if ($action == 'delete_email') {
        foreach ($_POST['delete'] as $mail_id=>$v)
        db_query("DELETE FROM $tables[mail_spool] WHERE mail_id='$mail_id'");
    }

    if ($action == 'extend') {
        $current_time = constant('CURRENT_TIME');
        foreach ($_POST['delete'] as $mail_id=>$v)
            db_query("UPDATE $tables[mail_spool] SET created=$current_time, send=$current_time WHERE mail_id='$mail_id'");
    }

    if ($action == 'check_email' && !empty($_POST['email'])) {
        cw_call('cw_send_mail', array($config['Company']['site_administrator'], $_REQUEST['email'],'mail/'.$_REQUEST['subject'],'mail/'.$_REQUEST['body']));
    }

    if ($action == 'pause_email_send') {
        db_query("REPLACE $tables[config] (name, config_category_id, value) values ('pause_email_sending', 1, '$pause_email_value')");
        cw_add_ajax_block(array(
            'id' => 'pause_email_sending_container',
            'action' => 'append',
            'content' => ''
        ));
        return;
    }

    cw_header_location("index.php?target=$target");
}


$mail_templates = cw_files_get_dir($app_dir.$app_skin_dir.'/mail',1,true);
foreach ($mail_templates as $mt) {
    $mt = str_replace($app_dir.$app_skin_dir.'/mail/','',$mt);
    if (strpos($mt,'_subj.tpl')!==false) $subjects[] = $mt;
    else $bodies[] = $mt;
}

$mail_spool_total = cw_query_first_cell("SELECT count(*) FROM $tables[mail_spool]");
$mail_spool = cw_query("SELECT mail_id, mail_to, subject, body, created, send FROM $tables[mail_spool] ORDER BY mail_id DESC LIMIT 20");
$smarty->assign(array('subjects'=>$subjects, 'bodies' => $bodies));
$smarty->assign('mail_spool_total', $mail_spool_total);
$smarty->assign('mail_spool', $mail_spool);

$smarty->assign('pause_email_sending', $config['pause_email_sending']);
// E-mail


$smarty->assign('main', 'mail_queue');


