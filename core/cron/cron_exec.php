<?php
set_time_limit(2*60*60); // 2 hours

header('Content-type: text/plain');

define('CW_CRON_TIMEOUT', 60*30); // Task considered as hanged after timeout in seconds

// Get manual_run if any
$manual_run = $_REQUEST['manual_run'];

$log = array();
$log['init'] = 'cron started as '.php_sapi_name().'.'.(empty($manual_run)?'':' Run specifically: '.$manual_run);

$log_name = 'cron/cron';

// Check security code
if ($_REQUEST['code'] != $config['Security']['cron_code']) {
	$log['error'] = 'Unauthorized access. Wrong code.';
	cw_log_add($log_name, $log, false);
	die($log['error']);
}

cw_load('cron');

$time = time();

if (empty($manual_run)) {
// Schedule the work

  
    // Init params
    $last_run = unserialize($config['last_cron_run']);

    if (!is_array($last_run)) {
        $last_run = array(
            'counter' => 0,
            'regular'  => 0,
            'hourly' => 0,
            'daily' => 0,
            'weekly' => 0,
            'biweekly' => 0,
            'monthly' => 0,
            'annually' => 0,
            'queue' => array(),
            'flag' => 0,
        );
    }
    if (!is_array($last_run['queue'])) {
        $last_run['queue'] = array();
    }
    $run_periods = array(
        'regular'   => 59,
        'hourly'    => SECONDS_PER_HOUR,
        'daily'     => SECONDS_PER_DAY,
        'weekly'    => SECONDS_PER_WEEK,
        'biweekly'  => SECONDS_PER_WEEK*2,
        'monthly'   => SECONDS_PER_DAY*30,
        'annually'  => SECONDS_PER_DAY*365,
    );

    $counter = ++$last_run['counter'];
    $log['init'] = '#'.$counter.': '.$log['init'];
    
	// New cron handlers are functions
	// Subscribe to one of this event with all your crontab functions, see cw_event_listen
	// on_cron_regular, on_cron_hourly, on_cron_daily and so on (see above)
    $tasks = $last_run['queue'];
	foreach ($run_periods as $p=>$s) {
		if ($time - $last_run[$p] > ($s-10)) { // 10 seconds gap for crontab/script starting process
            $on_cron = cw_get_hooks('on_cron_'.$p);
            unset($on_cron[0]);
            if (is_array($on_cron)) {
                $on_cron =  array_fill_keys($on_cron,$p);
                $tasks = cw_array_merge($tasks, $on_cron); // Collect all tasks for execution
            }
            $last_run[$p] = $time;
		}
	}
    
    $log['scheduled'] = $tasks;
    $log['execution'] = '#'.$counter.': Exec at '.date('H:i:s',time());
    $executed = 0;
    
    include_once  $app_main_dir.'/include/templater/plugins/modifier.id.php';
      
    
    foreach ($tasks as $task=>$period) {
        
        $sys_msg_name = 'cron_task_'.md5($task);
        $sys_msg = cw_system_message($sys_msg_name);
        
        // Check if task hangs
        if ($sys_msg) {
            // Hangs longer than timeout - error to log and remove from current queue; 
            //       less   than timeout - warning to log but leave in queue to exec next time
            if ($time - $sys_msg['date'] > CW_CRON_TIMEOUT) {
                $log[$task] = 'Error! Task removed from queue because previous execution is not finished '.($time - $sys_msg['date']).' sec.';
                unset($tasks[$task]);
            } else {
                $log[$task] = 'Warning! Task postponed because previous execution is not finished '.($time - $sys_msg['date']).' sec.';
            }
            continue;
        }
        
        // Exec all regular tasks and one scheduled
        if ($period == 'regular' || $executed == 0) {
            // Exec independent script
            // Attention! This exec() does not wait until end of the task, it runs in background.
	    $log['exec'][$task] = "php $app_main_dir/cron/index.php ".$config['Security']['cron_code'].' '.escapeshellarg($task).' '.$counter.' > /dev/null 2>/dev/null &';
            $log[$task] = exec("php $app_main_dir/cron/index.php ".$config['Security']['cron_code'].' '.escapeshellarg($task).' '.$counter.' > /dev/null 2>/dev/null &');

            // Give 10 sec for execution before next task.
            // It doesn't mean that task must be finished for sure, but minimize server/DB load and chance of tasks collision. 
            sleep(10);
            
            if (empty($log[$task])) $log[$task] = date('H:i:s',time()).": see log $var_dirs[log]/".$log_name .'_'.smarty_modifier_id($task).'-'.date('ymd').'.php';
            unset($tasks[$task]);
        }
        
        // Raise flag: one non-regular task is executed
        if ($period != 'regular') {
            $executed++;
        }
        
    }

    // Save other tasks to queue until next crontab start
    $last_run['queue'] = $tasks;
    db_query("REPLACE $tables[config] (name, config_category_id, value) values ('last_cron_run',1,'".db_escape_string(serialize($last_run))."')");

    // Check all hanged tasks
    $sys_msgs = cw_system_messages(SYSTEM_MESSAGE_INTERNAL, true);
    foreach ($sys_msgs as $sys_msg) {
        if (
            strpos($sys_msg['code'],'cron_task_') === 0 &&
            ($time - $sys_msg['date']) > (CW_CRON_TIMEOUT+60*5) &&
            strpos($sys_msg['message'],'GO ') === 0
           ) {  
                $task  = str_replace('GO ','',$sys_msg['message']);
                $sys_msg_name = 'cron_task_'.md5($task);
                $log['warning'][$task] = '<font style="color: red;">Cron task hangs.</font> <br />Cron task "'.$task.'" is not finished '.($time - $sys_msg['date']).' sec.';
                $log['warning'][$task] .= ' Check log, make sure the script works and delete this error.';
                cw_system_messages_add($sys_msg_name,$log['warning'][$task],SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_ERROR);
                $log['warning'][$task] = strip_tags($log['warning'][$task]);
        }
    }

} else {
// Do exact work
    $log['init'] = '#'.$_REQUEST['counter'].': '.$log['init'];
    
    $sys_msg_name = 'cron_task_'.md5($manual_run);
    
    // Raise flag
    cw_system_messages_add($sys_msg_name,"GO $manual_run",SYSTEM_MESSAGE_INTERNAL, SYSTEM_MESSAGE_ERROR);

    include_once  $app_main_dir.'/include/templater/plugins/modifier.id.php';
    $log_name .= '_'.smarty_modifier_id($manual_run);
    $log[$manual_run] = cw_call($manual_run, array($time, $_REQUEST['counter']));
    // Standalone script from cron folder can be executed separately
    $log['include:'.$manual_run] = cw_include($area.'/'.$manual_run.'.php');
    
    // Flush flag
    cw_system_messages_delete($sys_msg_name);
}

$log['end'] = '#'.$counter.': Cron ended at '.date('H:i:s',time());

cw_log_add($log_name,$log, false);

print_r($log);

exit(0); // do not return back to display functions
