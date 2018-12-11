<?php
$tables['datahub_dbg_sessions'] = 'cw_datahub_dbg_sessions';

$dbg_sessions = cw_query("select from_unixtime(split_string(session_key, '|',2)) as sess_time, split_string(session_key, '|',2) as sess_ts, session_key, max(date) as session_date from cw_datahub_dbg_sessions group by session_key order by session_date desc");

foreach ($dbg_sessions as $ds) {
print("<h2>".str_replace($ds['sess_ts'], trim($ds['sess_time'],'0.'), $ds['session_key'])."</h2>");
    $traced_files = array(); 
    $sess_logs = cw_query("select * from cw_datahub_dbg_sessions where session_key='$ds[session_key]'");
    foreach ($sess_logs as $sl) {
        if (empty($sl['backtrace'])) continue;
        $bt_lines = explode("\n", $sl['backtrace']); 
        foreach ($bt_lines as $btl) {
            $btl_parts = explode(':', $btl);
            $btl_parts[0] = trim(str_replace($app_dir, '', $btl_parts[0]));
 
            if (isset($traced_files[$btl_parts[0]])) {
                $traced_files[$btl_parts[0]][$btl_parts[1]] = 1; 
            } else {
                $traced_files[$btl_parts[0]] = array($btl_parts[1]=>1); 
            } 
        } 
    }  
/*
    print('<pre>');
    print_r($traced_files);
    print('</pre>');
*/
    foreach ($traced_files as $f_name=>$f_trace) { 
        print("<a href='index.php?target=datahub_dbg_session_log&dbg_file=".urlencode($f_name)."&dbg_sess_key=".urlencode($ds['session_key'])."'><b>$f_name ".count($f_trace)." call(s)</b></a><br><br>");
    }

}

if (empty($dbg_sessions)) 
   print("<H1>No sessions saved</h1>");

die;
