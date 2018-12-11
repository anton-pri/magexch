<?php

global $csvxc_field_types;
global $csvxc_import_issues_flag;

if($mode=='flexible_import'){
    
    if($action=='import_file'){

        mb_internal_encoding('UTF-8');
        setlocale(LC_ALL, "en_US.UTF-8");

        $csvxc_import_issues_flag = false;

        $rules = array('profile_id' => '','import_type' => '');
        $data  = array('profile_id'=> $profile_id, 'import_type'=> $import_type, 'import_file'=>$_FILES['import_file']);
        $fill_error = cw_call('cw_error_check', array(&$data, $rules));
        if($import_type == 'server' && !$server_filenames){
            $index = substr_count($fill_error, "\n") +($fill_error!=''? 1 : 0);
            $fill_error   .= "\n".++$index.'a . '.cw_get_langvar_by_name('err_field_select_file');
        }

        if($fill_error){
            cw_add_top_message($fill_error, 'E');
        }else{

            cw_display_service_header("Running flexible import");

            $parsed_file = cw_flexible_import_run_profile($profile_id, $server_filenames, $_FILES['import_file']);

            if($parsed_file['err'])
                cw_add_top_message($parsed_file['err'], 'E');
            else
                cw_add_top_message(cw_get_langvar_by_name('lbl_import_success').($csvxc_import_issues_flag?"<br>Though, there are some issues that need your attention.<br> Please check the import log files.":''), 'I');
        }
        cw_header_location("index.php?target=$target&mode=$mode");
    } elseif ($action == "delete_files") {
        if($import_type == 'server' && !$server_filenames){
            $index = substr_count($fill_error, "\n") +($fill_error!=''? 1 : 0);
            $fill_error   .= "\n".++$index.'a . '.cw_get_langvar_by_name('err_field_select_file');
        } else {
            $errors_msgs = array();  
            foreach($server_filenames as $s_file){
                if (!unlink(fi_files_path.$s_file)) {
                    $errors_msgs[] = "Cant delete file $s_file, please check file permissions";
                } 
            } 
            if ($errors_msgs) { 
                cw_add_top_message(implode("<br>", $errors_msgs), 'E');
            } else {
                cw_add_top_message(cw_get_langvar_by_name('lbl_delete_success'), 'I');
            }
        }
        
        cw_header_location("index.php?target=$target&mode=$mode");
    }

    $search_prefilled['files']          = cw_flexible_import_files_dir();
    $search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
    $search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
    $search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 100);
    $search_prefilled['page']           = ($page ? $page : 1);

    $profiles       = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));
    $total_profiles = cw_query_first_cell("SELECT COUNT(*) from $tables[flexible_import_profiles]");
    $navigation     = cw_core_get_navigation($target, $total_profiles, $page, $items_per_page);
    $navigation['script'] = "index.php?target=import&mode=flexible_import";
    $navigation['objects_per_page'] = $search_prefilled['items_per_page'];

    $smarty->assign('navigation', $navigation);
    $smarty->assign('search_prefilled', $search_prefilled);
    $smarty->assign('profiles', $profiles);
    $smarty->assign('main', 'flexible_import');

}

