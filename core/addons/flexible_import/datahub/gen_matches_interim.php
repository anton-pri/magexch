<?php

$gen_match_time_limit = 6*3600;

global $is_interim;
$is_interim = 1;

global $new_only;
$new_only = 1;

global $no_cache_rebuild;
$no_cache_rebuild = false;

global $gen_match_incl;
$gen_match_incl = 1;
$interim_ext = '_interim';

global $gen_limit;
$gen_limit = 1000;

$lock_file_path = $config['flexible_import']['flex_import_files_folder'].$interim_ext.'/gen_matches.lock';
if (file_exists($lock_file_path)) {
    $last_gen_match_start = file_get_contents($lock_file_path);
    $gen_match_runs = time() - intval($last_gen_match_start); 
    if ($gen_match_runs > $gen_match_time_limit) {
        file_put_contents($lock_file_path, time());
        print("<h2>Generate matches process runs already $gen_match_runs seconds, it seems have stalled. Restarting it.</h2><br>");
    } else {
        die('Generate matches process is currently running'); 
    }
} else {
    file_put_contents($lock_file_path, time());
    print("<h2>Generate matches process is being started...</h2>");
}

if ($config['flexible_import']['fi_sheduled_generate_matches_interim'] == "Y") {
    cw_include('addons/flexible_import/datahub/generate_matches.php');
} else {
    print("<h3>but the background generate matches process is disabled. Please check flexible import addon settings.</h3>");   
}

@unlink($lock_file_path);
print("<h3>Generate matches process is closed</h3>");

die;
