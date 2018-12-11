<?php

function cw_dh_import_print($str, $tags_on=false) {
    cw_log_add('datahub_cron_import', $str);
    print($str);

    cw_datahub_add_log_entry(array('message'=>($tags_on?$str:strip_tags($str)), 'source'=>'Automated Import'));
}

if ($config['flexible_import']['fi_enable_automated_stock_update'] != 'Y') {
    die('Automated stock update is disabled');
}

$is_import_locked = file_get_contents($var_dirs['flexible_import'].'/datahub.lock');

if ($is_import_locked) {
    $is_import_locked_data = explode('|', $is_import_locked); 
    if ($is_import_locked_data[0] != 'autoupdate_script_lock') {
        die("Automated import is locked at ".date("Y-m-d\TH:i:s", $is_import_locked_data[1]));
    }
}

if (isset($reset_stage)) {
    cw_array2insert('config', array('name'=>'datahub_import_stage', 'value'=>$reset_stage),true);
    cw_array2insert('config', array('name'=>'datahub_import_step', 'value'=>0),true);
}

set_time_limit(86400);
error_reporting(E_ALL);

$import_interval = 5;
if (!empty($config['flexible_import']['fi_datahub_import_interval']))
    $import_interval = intval($config['flexible_import']['fi_datahub_import_interval']);  


//$is_import_running = intval(cw_query_first_cell("select value from $tables[config] where name='datahub_is_import_running'"));
$is_import_running = file_get_contents($var_dirs['flexible_import'].'/automated_import.lock');//$is_import_locked[1]; 

cw_dh_import_print("Cron import script is accessed at ".date("Y-m-d\TH:i:s")."<br>");

if (!$is_import_running) {

    $dh_import_last_time_done = intval(cw_query_first_cell("select value from $tables[config] where name='datahub_import_last_time_done'"));
    if ($dh_import_last_time_done) {
        if (time()-$dh_import_last_time_done > 60*$import_interval) {
            cw_array2insert('config', array('name'=>'datahub_import_last_time_done', 'value'=>0),true); 
        } else {
            cw_dh_import_print('Next import process will start in '.(($dh_import_last_time_done+60*$import_interval)-time()+60).' sec'); 
            die;  
        }   
    }

    file_put_contents($var_dirs['flexible_import'].'/datahub.lock', 'autoupdate_script_lock|'.time());
    file_put_contents($var_dirs['flexible_import'].'/automated_import.lock', time());

    $dh_import_stage = intval(cw_query_first_cell("select value from $tables[config] where name='datahub_import_stage'"));
    if (!$dh_import_stage) {
        cw_dh_import_print("Placed lock file datahub.lock to block manual run(<a href='index.php?target=datahub_release_lock'>clear lock</a>)", 1);
        $dh_import_stage = 1;
        cw_array2insert('config', array('name'=>'datahub_import_stage', 'value'=>$dh_import_stage),true);
    } 

    $dh_import_step = intval(cw_query_first_cell("select value from $tables[config] where name='datahub_import_step'"));

    //cw_array2insert('config', array('name'=>'datahub_is_import_running', 'value'=>time()),true);

    if ($dh_import_stage == 1) {
        cw_dh_import_print("<h2>Starting step #$dh_import_step of stage $dh_import_stage...</h2>");
        require('core/addons/flexible_import/datahub/function_pos_update.php');
        $next_step = datahub_import_pos_update($dh_import_step, $is_web_mode);
    } elseif($dh_import_stage == 2) {
        cw_dh_import_print("<h2>Starting step #$dh_import_step of stage $dh_import_stage...</h2>");
        require('core/addons/flexible_import/datahub/function_calc_output.php');
        $next_step = datahub_import_calc_output($dh_import_step, $is_web_mode);
    } elseif ($dh_import_stage == 3) {
        cw_dh_import_print("<h2>Starting step #$dh_import_step of stage $dh_import_stage...</h2>");
        // Price, cost, inventory
        require('core/addons/flexible_import/datahub/function_run_cw_update_reduced.php');
        $next_step = datahub_import_run_cw_update_reduced($dh_import_step, $is_web_mode);
//    } elseif ($dh_import_stage == 4) {
        cw_dh_import_print("<h2>Starting step #$dh_import_step of stage $dh_import_stage...</h2>");
        require('core/addons/flexible_import/datahub/function_transfer_after_import_reduced.php');
        $next_step = datahub_import_transfer_after_import_reduced($dh_import_step, $is_web_mode);
/*
    } elseif ($dh_import_stage == 5) {
        cw_dh_import_print("<h2>Starting step #$dh_import_step of stage $dh_import_stage...</h2>");
        require('core/addons/flexible_import/datahub/function_step_pos_update.php');
        $next_step = datahub_import_step_pos_update($dh_import_step, $is_web_mode);
    } elseif ($dh_import_stage == 6) {
        cw_dh_import_print("<h2>Starting step #$dh_import_step of stage $dh_import_stage...</h2>");
        require('core/addons/flexible_import/datahub/function_export_new_pos.php');
        $next_step = datahub_import_export_new_pos($dh_import_step, $is_web_mode);
    } elseif ($dh_import_stage == 7) {
        cw_dh_import_print("<h2>Starting step #$dh_import_step of stage $dh_import_stage...</h2>");
        require('core/addons/flexible_import/datahub/function_export_changed_pos.php');
        $next_step = datahub_import_export_changed_pos($dh_import_step, $is_web_mode);
*/
    } else {
        cw_dh_import_print("<h2>All steps and stages are completed. Resetting process.</h2>");
        $dh_import_stage = 0;
        cw_array2insert('config', array('name'=>'datahub_import_stage', 'value'=>$dh_import_stage),true);
        $next_step = 0;
        cw_array2insert('config', array('name'=>'datahub_import_last_time_done', 'value'=>time()),true);
    }

    if ($dh_import_stage) { 
        if ($next_step > 0) { 
            cw_dh_import_print("<h2>Next step is #$next_step</h2>");
        } else {
            cw_dh_import_print("<h2>Current import stage #$dh_import_stage is completed</h2>");

            global $datahub_import_stop4manual;
            if ($datahub_import_stop4manual) { 
                cw_dh_import_print("<h2>ERROR! Cron import has stopped at stage #$dh_import_stage. Need manual check!</h2>");
                cw_log_add('datahub_import_stop4manual', "<h2>ERROR! Cron import has stopped at stage #$dh_import_stage. Need manual check! Import has been reset.</h2>");

                $dh_import_stage = 0;
                cw_array2insert('config', array('name'=>'datahub_import_stage', 'value'=>$dh_import_stage),true);
                $next_step = 0;

            } else {
                $dh_import_stage++;
                $next_step = 0;
                cw_array2insert('config', array('name'=>'datahub_import_stage', 'value'=>$dh_import_stage),true);
            }
        }
    }

    cw_array2insert('config', array('name'=>'datahub_import_step', 'value'=>$next_step),true);

    @unlink($var_dirs['flexible_import'].'/automated_import.lock');

    //cw_array2insert('config', array('name'=>'datahub_is_import_running', 'value'=>0),true);
    if ($dh_import_stage == 0) {  
        if (!$dh_import_stage && file_exists($var_dirs['flexible_import'].'/datahub.lock'))
            cw_dh_import_print("Released lock file datahub.lock");

        @unlink($var_dirs['flexible_import'].'/datahub.lock');
    }

} else {

    $dh_import_stage = intval(cw_query_first_cell("select value from $tables[config] where name='datahub_import_stage'"));
    $dh_import_step = intval(cw_query_first_cell("select value from $tables[config] where name='datahub_import_step'"));

    cw_dh_import_print("Process is already running for ".(time()-$is_import_running)." sec. Stage: $dh_import_stage, Step: $dh_import_step. Started at ".date("Y-m-d\TH:i:s", $is_import_running));
    if (time()-$is_import_running > 10*60) {
        unlink($var_dirs['flexible_import'].'/automated_import.lock');
        cw_dh_import_print("Released lock file automated_import.lock by timeout");
    }
}

 
die;
