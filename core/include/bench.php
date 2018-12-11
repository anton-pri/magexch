<?php 
/**
 * Show or log bench result
 */


if (!empty($__bench) && (constant('BENCH_DISPLAY') ||  constant('BENCH_LOG'))) {

    $smarty->assign('bench_max_memory',memory_get_peak_usage(true));
    $smarty->assign_by_ref('bench',$__bench);
    
    if (constant('BENCH_LOG')) {
        for ($i=1; $i<$__bench_counter; $i++) {
            $__bench_timelog[strval($__bench[$i]['start_time']-$__start_mictotime)] = $i;
            $__bench_timelog[strval($__bench[$i]['end_time']-$__start_mictotime)] = -1*$i;
        }
        ksort($__bench_timelog, SORT_NUMERIC);
        $smarty->assign_by_ref('bench_timelog',$__bench_timelog);
	}

    if (constant('BENCH_DISPLAY')) {
        $smarty->display($app_dir.'/'.$app_skin_dir.'/debug/bench.tpl');
    }

    if (constant('BENCH_LOG')) {
        cw_log_add('bench_exec',$smarty->fetch($app_dir.'/'.$app_skin_dir.'/debug/bench2.tpl'));
    }




}
