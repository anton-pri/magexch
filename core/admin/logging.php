<?php

cw_load('files');

$logging_search = &cw_session_register('logging_search');
$logging_filter = &cw_session_register('logging_filter');

if (empty($logging_search)) 
    $logging_search = array('sortby'=>'date', 'sortdir'=>0);


$log_columns = &cw_session_register('log_columns');

if (empty($log_columns)) {
    $log_columns = array(
        'current_area' => array('title'=>"Area"),
        'date' => array('title'=>"Date",'fixed'=>1),
        'is_logged' => array('title'=>"User Is Logged"),
        'REQUEST_URI' => array('title'=>"URI",'fixed'=>1),
        'REQUEST_METHOD' => array('title'=>"Method",'display'=>1),
        'GET_POST' => array('title'=>"GET/POST"),
        'target_code' => array('title'=>"target_code",'display'=>1),
        'cwsid' => array('title'=>"cwsid / call id"),
        'customer_id' => array('title'=>"customer_id"),
        'HTTP_REFERER' => array('title'=>"REFERER"),
        'REDIRECT_URL' => array('title'=>"REDIRECT_URL"),
        'IP' => array('title'=>"IP"),
    );
}

//accept filter/grouping params and reload with filtered list
if (isset($_GET['sortby']) || isset($_GET['sortdir'])) {

    if (isset($_GET['sortby'])) {
        if (in_array($_GET['sortby'], array('date', 'is_logged','current_area', 'REQUEST_URI', 'REQUEST_METHOD', 'GET_POST', 'target_code', 'cwsid', 'HTTP_REFERER', 'REDIRECT_URL'))) 
            $logging_search['sortby'] = $_GET['sortby'];
        else
            $logging_search['sortby'] = 'date';
    }

    if (isset($_GET['sortdir'])) {
        if (in_array($_GET['sortdir'],array(0,1))) 
            $logging_search['sortdir'] = $_GET['sortdir'];
        else
            $logging_search['sortdir'] = 0;
    }
    cw_header_location('index.php?target=logging');
}

$where_conditions = array();
$text_log_filters = array('REQUEST_URI', 'cwsid','call_id', 'HTTP_REFERER', 'REDIRECT_URL','IP');
if (!empty($logging_filter)) {
    if (!empty($logging_filter['date'])) {
        if (!empty($logging_filter['date']['date_start'])) 
            $where_conditions[] = "ld.date >= ".$logging_filter['date']['date_start'];
        if (!empty($logging_filter['date']['date_end'])) 
            $where_conditions[] = "ld.date <= ".$logging_filter['date']['date_end'];
    }

    if (!empty($logging_filter['date_int'])) {
        if (!empty($logging_filter['date_int']['date_start']))
            $where_conditions[] = "ld.date >= ".$logging_filter['date_int']['date_start'];
        if (!empty($logging_filter['date_int']['date_end']))
            $where_conditions[] = "ld.date <= ".$logging_filter['date_int']['date_end'];
    }


    foreach ($text_log_filters as $tlf) {
        if (!empty($logging_filter[$tlf])) {
            $where_conditions[] = "ld.$tlf like ('%".db_escape_string_once($logging_filter[$tlf])."%')";
        }
    }
    if (!empty($logging_filter['is_logged'])) { 
        $is_logged_qry = array(); 
        if ($logging_filter['is_logged']['yes'])  
            $is_logged_qry[] = "ld.customer_id > 0";
        if ($logging_filter['is_logged']['no'])
            $is_logged_qry[] = "ld.customer_id = 0";
        $where_conditions[] = "(".implode(" or ", $is_logged_qry).")";
    }

    if (trim($logging_filter['customer_id']) !== '') {
        $where_conditions[] = "ld.customer_id = '".intval($logging_filter['customer_id'])."'";
    }

    if (!empty($logging_filter['current_area'])) {
        $where_conditions[] = "ld.current_area in ('".implode("','", array_keys($logging_filter['current_area']))."')";
    }

    if (!empty($logging_filter['REQUEST_METHOD'])) {
        $where_conditions[] = "ld.REQUEST_METHOD in ('".implode("','", array_keys($logging_filter['REQUEST_METHOD']))."')";
    }
    if (!empty($logging_filter['target_code'])) {
        $where_conditions[] = "ld.target_code in ('".implode("','", array_keys($logging_filter['target_code']))."')";
    }
    if (!empty($where_conditions)) 
        $where_string_qry = " where ".implode(' and ', $where_conditions);

    $smarty->assign('logging_filter',$logging_filter);
}

//select logging data according to filter
switch ($logging_search['sortby']) {
    case 'date':
    case 'is_logged':
    case 'current_area': 
    case 'REQUEST_URI':
    case 'REQUEST_METHOD':
    case 'GET_POST':
    case 'target_code':
    case 'cwsid':
    case 'HTTP_REFERER':
    case 'REDIRECT_URL':
    $orderby_qry = " order by `$logging_search[sortby]` ".($logging_search['sortdir']?'desc':'asc');
    break;
} 

if ($REQUEST_METHOD == "POST") {
    if ($action == "filter_logs") {
        $date_fields = array (
            'date' => array('date_start' => 0, 'date_end' => 1),
        );

        cw_core_process_date_fields($logs_filter, $date_fields, array());

        foreach ($log_columns as $lc_k=>$lc_v) {
            if (!intval($lc_v['fixed'])) {
                $log_columns[$lc_k]['display'] = !empty($logs_cols[$lc_k]);
            }
        }
        $logging_filter = $logs_filter;
    } elseif($action == 'archive_logs') {
         if (!file_exists($var_dirs['logs_archive'])) 
             mkdir($var_dirs['logs_archive']);

         $curr_year = date('Y'); 
         if (!file_exists($var_dirs['logs_archive'].'/'.$curr_year)) 
             mkdir($var_dirs['logs_archive'].'/'.$curr_year); 

         $curr_month = date('m');
         if (!file_exists($var_dirs['logs_archive'].'/'.$curr_year.'/'.$curr_month))
             mkdir($var_dirs['logs_archive'].'/'.$curr_year.'/'.$curr_month);

         $arch_log_file_name = $curr_year.'/'.$curr_month.'/'.date('d_H_i_s').'.csv';
         $arch_log_name = $var_dirs['logs_archive'].'/'.$arch_log_file_name;
         $archive_where_string_qry = $where_string_qry;
         $arch_select_qry = "\"select ld.* from $tables[logged_data] as ld $archive_where_string_qry\"";
         $mysql_db = $app_config_file['sql']['db'];
         $mysql_user = $app_config_file['sql']['user']; 
         $mysql_password = $app_config_file['sql']['password'];
         $mysql_host = $app_config_file['sql']['host'];
         $shell_comm = "echo $arch_select_qry | mysql --host=$mysql_host --user=$mysql_user --password=$mysql_password $mysql_db > $arch_log_name";
         shell_exec($shell_comm);
         if (file_exists($arch_log_name)) {  
             cw_add_top_message("Current log saved to archive: <a style='color:white' href='$var_dirs_web[logs_archive]/$arch_log_file_name'>$arch_log_name</a>");
             if ($drop_archived == "1") {
                 db_query("delete from ld using $tables[logged_data] as ld $archive_where_string_qry");
             }
         } else 
             cw_add_top_message('Cannot save log to file: '.$arch_log_name, 'E');

    }
    cw_header_location('index.php?target=logging');
}


$total_items = cw_query_first_cell("select count(*) from $tables[logged_data] as ld $where_string_qry"); 

$navigation = cw_core_get_navigation($target, $total_items, $page);
$navigation['script'] = 'index.php?target='.$target;
$smarty->assign('navigation', $navigation);
$smarty->assign('page', $page);

$limit_qry = " LIMIT $navigation[first_page], $navigation[objects_per_page]";

$logged_data = cw_query($s = "select ld.*, IF(ld.customer_id>0,1,0) as is_logged from $tables[logged_data] as ld $where_string_qry $orderby_qry $limit_qry");

$session_ids = array();
foreach ($logged_data as $ld_k=>$ld_v) {
    $session_ids[$ld_v['cwsid']] = 1;
    $logged_data[$ld_k]['GET_POST'] = unserialize($ld_v['GET_POST']);
}
$smarty->assign('logged_data', $logged_data);

if ($logging_filter['cwsid']) {
    $_sess_data = cw_query("select * from $tables[logged_data_sessions] where cwsid in ('" . db_escape_string_once($logging_filter['cwsid']) . "')");
    $sess_data = array();
    foreach ($_sess_data as $s_data) {
        $s_data['SERVER'] = unserialize($s_data['SERVER']);
        $s_data['user_account'] = unserialize($s_data['user_account']);
        $_cwsid = $s_data['cwsid'];
        unset($s_data['cwsid']);
        $sess_data[$_cwsid] = $s_data;
    }
    $smarty->assign('sess_data', $sess_data);
}

$unq_target_code = cw_query_column("select distinct target_code from $tables[logged_data] as ld order by target_code", 'target_code');
$smarty->assign('unq_target_code', $unq_target_code);


$_all_arch_files = cw_files_get_dir($var_dirs['logs_archive'],1,true);

$all_arch_files = array();
foreach ($_all_arch_files as $f_name) {
    if (is_file($f_name)) 
        $all_arch_files[] = substr($f_name,strlen($var_dirs['logs_archive'])); 
}

asort($all_arch_files);

$smarty->assign('all_arch_files', $all_arch_files);


$smarty->assign('logging_search',$logging_search);

$smarty->assign('log_columns',$log_columns);

$smarty->assign('main', 'logging');

