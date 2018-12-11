<?php

$profile_id = intval($profile_id);

$fi_profile = cw_query_first("select * from $tables[flexible_import_profiles] where id='$profile_id'");

if (empty($fi_profile))
    cw_header_location("index.php?target=datahub_raw_import");

$is_interim = (strpos(strtolower($fi_profile['name']),'interim')!==false);

$is_beva = (strpos(strtolower($fi_profile['name']),'beva')!==false);
if (!$is_beva)
    $is_vias = (strpos(strtolower($fi_profile['name']),'vias')!==false);
else 
    $is_interim = true;

$interim_ext = ($is_interim)?'_interim':'';

if ($action == 'reload') {
    if (!empty($hash2reload)) {
        $qry = "delete from $tables[flexible_import_loaded_files_hash] where profile_id='$profile_id' and hash='$hash2reload'";  
        db_query($qry);
//        die($qry);
        if ($is_beva) {
cw_add_top_message('Selected Beva file will be loaded automatically with next cron task', 'I'); 
        } elseif ($is_vias) {
cw_add_top_message('Selected Vias file will be loaded automatically with next cron task', 'I');
        } else {
            if ($config['flexible_import']['fi_datahub_autoload_profiles_interim'.$interim_ext]=='Y') 
                cw_add_top_message('Selected file will be loaded automatically with next cron task', 'I');   
            else 
                cw_add_top_message("Selected file will be loaded by click on \"Check and load new files\" button", 'I');  
        }
    }
    cw_header_location("index.php?target=datahub_profile_files&profile_id=$profile_id");
}

$fi_profile['options'] = unserialize($fi_profile['options']);

$smarty->assign('is_interim', $is_interim);

    $s_file = $fi_profile['recurring_import_path'];

    if (!file_exists($s_file))
        $s_file = rtrim($config['flexible_import']['flex_import_files_folder'].$interim_ext,'/').'/'.$s_file;

    if (!file_exists($s_file) && file_exists(fi_files_path.$s_file))
        $s_file = fi_files_path.$s_file;

    $file_names = array();

    if (is_dir($s_file)) {
        $dirfiles = scandir($s_file);
        $latest_file = '';
        foreach ($dirfiles as $d_file) {
            if (in_array($d_file, array('.', '..'))) continue;
            $d_file_full = rtrim($s_file,'/').'/'.$d_file;
            $file_hash = md5_file($d_file_full);   
            $hash_info = cw_query_first("select * from $tables[flexible_import_loaded_files_hash] where profile_id='$profile_id' AND hash='$file_hash'");  
            $file_names[filectime($d_file_full)] = array(
                'full_filename'=>$d_file_full,
                'filename'=>str_replace($config['flexible_import']['flex_import_files_folder'].$interim_ext,'',$d_file_full), 
                'hash_info'=>$hash_info, 
                'created'=>filectime($d_file_full), 
                'modified'=>filemtime($d_file_full)
            );
        }
    }

$smarty->assign('fi_profile', $fi_profile);

krsort($file_names);

$smarty->assign('file_names', $file_names);
$smarty->assign('main', 'datahub_profile_files');
