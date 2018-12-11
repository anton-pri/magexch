<?php

set_time_limit(2*60*60); // 2 hours

// Get manual_run if any
$manual_run = $_REQUEST['manual_run'];

$log = array();
$log['init'] = 'cron started as '.php_sapi_name().'.'.(empty($manual_run)?'':' Run specifically: '.$manual_run);

// Check security code
if ($_REQUEST['code'] != $config['Security']['cron_code']) {
	$log['error'] = 'Unauthorized access. Wrong code.';
	cw_log_add('cron', $log, false);
	die($log['error']);
}

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
    'regular' => 59,
    'hourly' => SECONDS_PER_HOUR,
    'daily' => SECONDS_PER_DAY,
    'weekly' => SECONDS_PER_WEEK,
    'biweekly' => SECONDS_PER_WEEK*2,
    'monthly' => SECONDS_PER_DAY*30,
    'annually' => SECONDS_PER_DAY*365,
);

$time = time();

// Check unfinished cron
if ($last_run['flag']) {
    // Flag is raised - previous cron runs
    // Log this issue
    cw_log_add('cron',"Warning: new cron started while previous #{$last_run['counter']} is not finished or crashed", false);
    
    if (($time - $last_run['regular']) < $run_periods['regular']*15) {
        echo "skip";
        // Skip this cron several "regular times" in case the previous execution does something normal but long
        exit(0);
    }

    // Flag is raised too long, something wrong with scheduled tasks
    // System message
    cw_system_messages_add('cron_not_finished',"New cron started while previous #{$last_run['counter']} is not finished or crushed",SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_ERROR);
    
}

$last_run['flag']++; // Cron started
$counter = ++$last_run['counter'];
db_query("REPLACE $tables[config] (name, config_category_id, value) values ('last_cron_run',1,'".db_escape_string(serialize($last_run))."')");

$time_dump = date('H:i', $time);
list($hour, $minute) = explode(':', $time_dump);

$log['init'] = '#'.$counter.': '.$log['init'];

cw_load('cron');

if (empty($manual_run)) {
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

    cw_log_add('cron',$log, false);
    
    $log = array();
    $log['execution'] = '#'.$counter.': Exec at '.date('H:i:s',time());
    $executed = 0;
    // Exec all regular tasks and one scheduled
    foreach ($tasks as $task=>$period) {
        if ($period == 'regular' || $executed == 0) {
            $log[$task] = cw_event($task, array($time, $last_run[$period]));
            if (empty($log[$task])) $log[$task] = null;
            unset($tasks[$task]);
        }
        if ($period != 'regular') {
            $executed++;
        }
    }
    
    $last_run['queue'] = $tasks;

} else {
	$log[$manual_run] = cw_call($manual_run, array($time, $time));
    
    // Standalone script from cron folder can be executed separately
    $log['include:'.$manual_run] = cw_include($area.'/'.$manual_run.'.php');
}

$last_run['flag'] = 0; // Cron finished
db_query("REPLACE $tables[config] (name, config_category_id, value) values ('last_cron_run',1,'".db_escape_string(serialize($last_run))."')");
cw_system_messages_delete('cron_not_finished');

$log['end'] = '#'.$counter.': Cron ended at '.date('H:i:s',time());

cw_log_add('cron',$log, false);

cw_call('cw_system_messages_delete', array('crontab_warning'));

exit(0); // do not return back to display functions
