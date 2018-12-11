<?php
$dbg_file = urldecode($dbg_file);
$dbg_sess_key = urldecode($dbg_sess_key);

$dbg_sess_key = cw_query_first_cell("select session_key from cw_datahub_dbg_sessions where session_key='$dbg_sess_key'");

if (empty($dbg_sess_key) || empty($dbg_file) || !file_exists($app_dir.$dbg_file)) cw_header_location("index.php?target=datahub_dbg_sessions");

$debug_tables = array(
'item_store2' => array(),
'pos'=> array(),
'xfer_products_SWE'=>array(),
'item'=>array(),
'cw_datahub_main_data'=>array(),
'feeds_item_compare'=>array()
);


print("<h1>$dbg_sess_key <br> $dbg_file</h1>");

$file_content = file_get_contents($app_dir.$dbg_file); 
if (empty($file_content)) cw_header_location("index.php?target=datahub_dbg_sessions");

$sess_logs = cw_query("select * from cw_datahub_dbg_sessions where session_key='$dbg_sess_key'");
$fc_lines = explode("\n", $file_content);
$disp_dbg = array();

foreach ($sess_logs as $sl) {
    if (empty($sl['backtrace'])) continue;
    $bt_lines = explode("\n", $sl['backtrace']); 
    foreach ($bt_lines as $btl) {
        $btl_parts = explode(':', $btl);
        $btl_parts[0] = trim(str_replace($app_dir, '', $btl_parts[0]));
        if ($btl_parts[0] != $dbg_file) continue;

        if (!isset($disp_dbg[$btl_parts[1]])) {
            $disp_dbg[$btl_parts[1]] = array(); 
        }

        $disp_dbg[$btl_parts[1]][] = $sl['dbg_ts'];
        
        

    }
}

print("<div id='start'></div><pre>");
foreach ($fc_lines as $lid=>$f_line) {

    $_f_line = htmlspecialchars($f_line, ENT_QUOTES);
    $_f_line = str_replace(' ','&nbsp;',$_f_line);
    if (in_array($lid+1, array_keys($disp_dbg)))
        $_f_line = "<span id='back_dbg_".($lid+1)."' style='background-color:yellow'><a href='#dbg_".($lid+1)."'>$_f_line</a></span>";
    else
        $_f_line = "<span>$_f_line</span>"; 

    print(($lid+1).": $_f_line<br>");
}
//print('<pre>');
//print(htmlspecialchars($file_content, ENT_QUOTES));
ksort($disp_dbg);
//print_r($disp_dbg);

print('</pre><br><br><br>'); 
//die;
foreach ($disp_dbg as $lid=>$tracks) {
    print("<hr><div id='dbg_$lid'>");
    print("<h3>Line: $lid   <a href='#back_dbg_$lid'>back</a></h3>");
//    print(implode(" ", array_keys($tracks)));
    if (count($tracks)>10) {print(implode(" ", array_keys($tracks))); continue;}
    foreach ($tracks as $dbg_ts) {
        foreach ($debug_tables as $dbg_tbl=>$dbg_cols) {
            $_dbg_tbl = $dbg_tbl."_dh_dbg";
            $dbg_data_all = cw_query($s="select * from `$_dbg_tbl` where dbg_ts='$dbg_ts'");
            if (empty($dbg_data_all)) {
                print("<b>$dbg_tbl</b><br>");
                print("<i>no data</i><br>"); continue;
            }

            foreach ($dbg_data_all as $dbg_data) {  
                unset($dbg_data['dbg_ts']);
                print("<b>$dbg_tbl</b>  $dbg_data[dbg_type]<br>");
                unset($dbg_data['dbg_type']);

                if (!empty($dbg_data)) {
                    print("<table border='1'>");    
                    print("<tr><td>".implode("</td><td>", array_keys($dbg_data))."</td></tr>");
                    print("<tr><td>".implode("</td><td>", $dbg_data)."</td></tr>");
                    print("</table>");
                }
            }
        }
    } 

    print("</div><br>");
}


die;
