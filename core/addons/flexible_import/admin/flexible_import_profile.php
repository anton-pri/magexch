<?php
cw_load('dev');
$fill_error = &cw_session_register('error', array());
$prefilled_data = &cw_session_register('prefilled_data', array());
$extra_mapping_tables = &cw_session_register('extra_mapping_tables', array());

global $tmp_load_tables;

if($mode=='flexible_import_profile'){
    $fill_error = array();

    if(trim($fi_profile['col_names_line_id'])=="")
        $fi_profile['col_names_line_id']= 'n';
    if($action=='test_profile' || $action =='import_file') {
        extract($_REQUEST);

        $fi_profile['import_file'] = $_FILES['import_file'];
        $url_id="";
        if($profile_id){
           $fi_profile['id'] = $profile_id;
           $url_id = "&profile_id=".$profile_id;
        }
        if(trim($fi_profile['col_names_line_id'])=="")
            $fi_profile['col_names_line_id']= 'n';

        if (get_magic_quotes_gpc()) {
            array_walk_recursive($prefilled_data, 'cw_flexible_import_strip_input');
        }
        $rules = array('name' => '','type' => '');

        $fill_error = cw_call('cw_error_check', array(&$fi_profile, $rules));

        if (trim($fill_error)!="") {
            $prefilled_data = $fi_profile;
            cw_add_top_message($fill_error, 'E');
            cw_header_location("index.php?target=$target&mode=$mode&action=check".$url_id);

        }

        if ($action =='test_profile') { 

            if ($fi_profile['import_src_type'] == 'T') {

                $prefilled_data = $fi_profile;

            } else {
                if (!empty($fi_profile['import_file']['name'])) {
                    $test_import_file = $fi_profile['import_file'];  
                } else {
                    $test_import_file = $prefilled_data['import_file'];
                }

                $prefilled_data = $fi_profile;
                $prefilled_data['import_file'] = $test_import_file;

                list($fi_profile, $prefilled_data) = cw_flexible_import_convert_sheet($test_import_file, $fi_profile, $prefilled_data);

                $prefilled_data['parsed_file'] = $parsed_file = cw_call('cw_flexible_import_parse_file',array($fi_profile, true));

                if (file_exists($prefilled_data['import_file']['tmp_name']) && !empty($fi_profile['import_file']['name'])) {
                    $new_tmp_path = $var_dirs['tmp'];
                    if (!file_exists($new_tmp_path)) 
                        mkdir($new_tmp_path);

                    $new_tmp_fname = $new_tmp_path.'/'.basename($prefilled_data['import_file']['tmp_name']);   
                    copy($prefilled_data['import_file']['tmp_name'], $new_tmp_fname);
                    $prefilled_data['import_file']['tmp_name'] = $new_tmp_fname;
                }
            }
        }
        if($parsed_file['err'])
            cw_add_top_message($parsed_file['err'], 'E');


        cw_header_location("index.php?target=$target&mode=$mode&action=check".$url_id);

    } elseif($action=='check'){



    }elseif($action=='save_profile'){
        if ($fi_profle['import_src_type'] == 'T') {
            
        } else {
            unset($fi_profile['test_file_demo_content']);

            $fi_profile['test_import_file'] = $prefilled_data['import_file'];
//cw_var_dump($prefilled_data);

            if (!empty($prefilled_data['parsed_file'])) {
                $fi_profile['parsed_columns'] = array(
                    'fields' => $prefilled_data['parsed_file']['fields'] 
                );
            }
        }

        $result = cw_call('cw_flexible_import_save_profile', array($fi_profile, $fi_profile['type']));

        if($result) {
            cw_header_location("index.php?target=import&mode=flexible_import_profile&step=mapping&profile_id=".(!empty($fi_profile['id'])?$fi_profile['id']:$result));
        }

    } elseif($action=='delete_profile'){

        if($profile_ids)
            cw_call('cw_flexible_import_delete_profile',array('profile_ids'=> $profile_ids));
        cw_header_location("index.php?target=import&mode=flexible_import");
    } elseif($action == 'mapping_tables_save') {
       $extra_mapping_tables = $fi_profile['extra_tables'];       

       cw_header_location("index.php?target=import&mode=flexible_import_profile&step=mapping&profile_id=$fi_profile[id]");

    } elseif($action == 'mapping_save') {

        $mapping_fields2save = array();
        $map_process2save = array();
        foreach ($mapping as $tbl_name => $tbl_fields) {
            foreach ($tbl_fields as $fld_name=>$fld_mapping)  {
                if (!empty($fld_mapping['imp_field']) || !empty($fld_mapping['custom_sql'])) {

                    if (!empty($fld_mapping['custom_sql']))
                        $fld_mapping['custom_sql'] = base64_encode(str_ireplace(array(/*'ALTER', 'CREATE TABLE', 'DROP', 'DELETE', 'INSERT', 'REPLACE INTO', 'UPDATE'*/),'',$fld_mapping['custom_sql']));

                    if (!isset($mapping_fields2save[$tbl_name]))  
                        $mapping_fields2save[$tbl_name] = array();
                    $mapping_fields2save[$tbl_name][$fld_name] = $fld_mapping;
                }     
            } 
            if (!empty($mapping_fields2save[$tbl_name]) && !empty($map_process[$tbl_name])) {
                $map_process2save[$tbl_name] = $map_process[$tbl_name];
                if (!empty($map_process2save[$tbl_name]['post_sql']))
                    $map_process2save[$tbl_name]['post_sql'] = base64_encode(str_ireplace(array(/*'ALTER', 'CREATE TABLE', 'DROP', 'INSERT'*/),'',$map_process2save[$tbl_name]['post_sql'])); 
                if (!empty($map_process2save[$tbl_name]['clean_table_condition']))
                    $map_process2save[$tbl_name]['clean_table_condition'] = base64_encode(str_ireplace(array('ALTER', 'CREATE TABLE', 'DROP', 'INSERT', 'REPLACE INTO'),'',$map_process2save[$tbl_name]['clean_table_condition']));   
            }
        }

        cw_array2update('flexible_import_profiles', array('mapping_data'=>serialize($mapping_fields2save), 'map_process'=>serialize($map_process2save)), "id=$fi_profile[id]"); 

        cw_header_location("index.php?target=import&mode=flexible_import_profile&step=mapping&profile_id=$fi_profile[id]");
    } elseif($profile_id){

        $profile = cw_call('cw_flexible_import_get_profile', array('params'=>array('id'=> intval($profile_id))));

        if ($step == "mapping") {
            if (!empty($profile['parsed_columns'])) {
                $profile['parsed_file'] = unserialize($profile['parsed_columns']);
                unset($profile['parsed_columns']);
            }
            $prefilled_data = $profile;
        } else {
            $test_import_file_name = $profile['test_import_file_name'];
            if (file_exists($test_import_file_name)) {
                $profile['file_name'] = $test_import_file_name;
                $profile['test_import_file']['tmp_name'] = $test_import_file_name;
                $prefilled_data = $profile;
                list($profile, $prefilled_data) = cw_flexible_import_convert_sheet($profile['test_import_file'], $profile, $prefilled_data);

                $prefilled_data['parsed_file'] = cw_call('cw_flexible_import_parse_file',array($profile, true));
                $prefilled_data['import_file'] = $prefilled_data['test_import_file'];
            } else {
                $prefilled_data = $profile;
            }  
        }

    } else {
            $prefilled_data = array();
    }



    if (!empty($prefilled_data['custom']))
    foreach($prefilled_data['custom'] as $k => $v){
        $prefilled_data['custom'][$k] = htmlentities($v);
    }

    if (!empty($prefilled_data['adv'])) 
    foreach($prefilled_data['adv'] as $key => $val){
        if(is_array($val)){
            foreach($val as $k => $v){
                if(!is_array)
                $prefilled_data['adv'][$key][$k] = htmlentities($v);
            }
            }else
            $prefilled_data['adv'][$key] = htmlentities($val);


    }

    if ($step == "mapping") {
        if (!empty($prefilled_data['mapping_data'])) 
            $prefilled_data['mapping_data'] = unserialize($prefilled_data['mapping_data']);
        else 
            $prefilled_data['mapping_data'] = array();

        if (!empty($prefilled_data['map_process'])) {
            $prefilled_data['map_process'] = unserialize($prefilled_data['map_process']);
            foreach ($prefilled_data['map_process'] as $mp_k=>$mp_v) {
                if (!empty($mp_v['post_sql']))  
                    $prefilled_data['map_process'][$mp_k]['post_sql'] = stripslashes(base64_decode($mp_v['post_sql']));
                if (!empty($mp_v['clean_table_condition']))
                    $prefilled_data['map_process'][$mp_k]['clean_table_condition'] = stripslashes(base64_decode($mp_v['clean_table_condition'])); 
            }
        } else
            $prefilled_data['map_process'] = array();

        $extra_mapping_tables = array_filter($extra_mapping_tables, function ($t) { return cw_query_first_cell("SHOW TABLES LIKE '".$t."'");} );

        if (empty($extra_mapping_tables) && !empty($prefilled_data['mapping_data'])) {
            foreach ($prefilled_data['mapping_data'] as $map_table => $map_data) {
                if (in_array($map_table, array_keys($tmp_load_tables))) continue;
                $extra_mapping_tables[] = $map_table; 
            } 
        } 

        $smarty->assign('core_tmp_load_tables', $tmp_load_tables);

        if (!empty($extra_mapping_tables)) {
            foreach ($extra_mapping_tables as $ext_tbl) {
                if (in_array($ext_tbl, array_keys($tmp_load_tables))) continue;
                $tmp_load_tables[$ext_tbl] = array('dynamic_field_set' => array('query'=>"desc $ext_tbl"));
            } 
        }

        foreach ($tmp_load_tables as $tbl_name => $tbl_data) {
            $unset_key = false;  
            $dynamic_field_set = array();
            foreach ($tbl_data as $tbl_field => $fld_data) {
                if ($tbl_field == 'dynamic_field_set') {
                    $unset_key = true;
                    if (!empty($fld_data['query'])) {        
                        $dynamic_field_set = cw_query($fld_data['query']);  
                    }
                }  
            } 
            if ($unset_key) {
                unset($tmp_load_tables[$tbl_name]['dynamic_field_set']);
            } 
            if (!empty($dynamic_field_set)) {   
                foreach($dynamic_field_set as $dfs_v) {
                    $fld_db_field = !empty($dfs_v['field'])?$dfs_v['field']:$dfs_v['Field'];
                    $fld_db_type = ($dfs_v['Type'] == 'int(11)')?'int':'text';
                    $tmp_load_tables[$tbl_name][$fld_db_field] = array('type' => $fld_db_type);
                }    
            }
        }

        foreach ($prefilled_data['mapping_data'] as $tbl_name => $tbl_fields) {
             foreach ($tbl_fields as $fld_name => $fld_mapping) {

                 if (!empty($fld_mapping['custom_sql'])) 
                     $fld_mapping['custom_sql'] = stripslashes(base64_decode($fld_mapping['custom_sql']));
                 if (isset($tmp_load_tables[$tbl_name])) {   
                     if (isset($tmp_load_tables[$tbl_name][$fld_name])) {
                         $tmp_load_tables[$tbl_name][$fld_name] = array_merge($tmp_load_tables[$tbl_name][$fld_name], $fld_mapping);  
                     }  
                 }
             } 
        } 
        $smarty->assign('tmp_load_tables', $tmp_load_tables);
    }

    $smarty->assign('step', $step);
    $smarty->assign('main', 'flexible_import_profile');

}

$extra_databases = array();
if (!empty($config['flexible_import']['fi_extra_tables_databases'])) {
   $extra_databases = explode(";", $config['flexible_import']['fi_extra_tables_databases']);
}
$extra_tables = array();
foreach ($extra_databases as $extra_db) {
    $_extra_tables = cw_query_column("show tables from $extra_db");
    foreach ($_extra_tables as $_et) {
        if ($extra_db != $app_config_file['sql']['db']) {
            $extra_tables[] = "$extra_db.`$_et`";
        } else {
            $extra_tables[] = $_et;   
        }
    } 
}

//$extra_tables = cw_query_column("show tables");
$smarty->assign('extra_tables', $extra_tables);

if (isset($prefilled_data))
    if ($prefilled_data['import_src_type'] == 'T' && !empty($prefilled_data['dbtable_src'])) {

        $prefilled_data['src_dbtable'] = array('fields'=>array(), 'data' => array());

        $dbsource_test_data = cw_query("SELECT * FROM $prefilled_data[dbtable_src] LIMIT 30"); 
        foreach ($dbsource_test_data as $dbsrc_line) {

            if (empty($prefilled_data['src_dbtable']['fields']['values']))  
                $prefilled_data['src_dbtable']['fields']['values'] = array_keys($dbsrc_line);

            $prefilled_data['src_dbtable']['data'][] = $dbsrc_line;

        }
    } 

$smarty->assign('prefilled_data', $prefilled_data);
