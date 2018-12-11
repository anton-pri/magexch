<?php
cw_load('speed_bar', 'attributes', 'config');

$top_message = &cw_session_register('top_message', array());

if ($mode == 'config') {

    if (!$domain_id) cw_header_location('index.php?target='.$target);

    if ($action == 'update') {
        if ($new_option) {
            list($category, $name) = explode(':', $new_option);
            $category_id = cw_query_first_cell("select config_category_id from $tables[config_categories] where category='".$category."'");
            cw_array2insert('domains_config', array('name' => $name, 'config_category_id' => $category_id, 'value' => $config[$category][$name], 'domain_id' => $domain_id), 1);
        }
        if (is_array($posted_data))
        foreach($posted_data as $cat_id=>$values)
            foreach($values as $name=>$val) {
                if ($del[$cat_id][$name] == 'Y') db_query("delete from $tables[domains_config] where domain_id='$domain_id' and name='$name' and config_category_id='$cat_id'");
                else db_query($sql="update $tables[domains_config] set value='$val' where domain_id='$domain_id' and name='$name' and config_category_id='$cat_id'");
            }
        cw_header_location('index.php?target='.$target.'&mode=config&domain_id='.$domain_id);
    }

    $domain_config = cw_md_get_config_values($domain_id);
    $smarty->assign('domain_config', $domain_config);
    $smarty->assign('categories', cw_config_get_categories(true));
    $smarty->assign('domain_id', $domain_id);

    $location[] = array(cw_get_langvar_by_name('lbl_domains'), 'index.php?target='.$target);
    $location[] = array(cw_get_langvar_by_name('lbl_edit_config'), '');
    $smarty->assign('main', 'config');
}
else {
    if ($action == 'add') {
        $ret = cw_md_domain_create($posted_data[0]);
        cw_header_location("index.php?target=$target");
    }

    if (in_array($action, array('update', 'delete'))) {
        foreach($posted_data as $k=>$v) {
            if ($action == 'delete' && $v['del'])
                cw_md_domain_delete($k);
            if ($action == 'update' && $k)
                cw_md_domain_update($k, $v);
        }
        cw_header_location("index.php?target=$target");
    }

    if ($action == 'copy_basic' && $confirmed == 'Y') {
        $data = cw_func_call('cw_md_domain_get', array('domain_id' => $domain_id));
        $return = false;
        if ($data['skin'] && $data['skin'] != $app_config_file['web']['skin'])
            $return = cw_md_copy_skin($app_dir.$app_config_file['web']['skin'], $app_dir.$data['skin'], '/');
        if (count($return)) $top_message = array('type' => 'E', 'content' => cw_get_langvar_by_name('lbl_mdm_error_copy_skin', array('log' => implode('<br/>', $return)), false, true));
        cw_header_location("index.php?target=$target");
    }
    elseif ($action == 'copy_basic') {
        $smarty->assign('main', 'copy');
        $data = cw_func_call('cw_md_domain_get', array('domain_id' => $domain_id));
        $smarty->assign('altskin', $data['skin']);
        $smarty->assign('domain_id', $domain_id);

     }


    if ($action == 'cleanup') {
        $data = cw_func_call('cw_md_domain_get', array('domain_id' => $domain_id));
        $return = false;
        if ($data['skin'] && $data['skin'] != $app_config_file['web']['skin'])
            $return = cw_md_cleanup_skin($app_dir.$app_config_file['web']['skin'], $app_dir.$data['skin'], '/');
        $smarty->assign('result', $return);
        $smarty->assign('altskin', $data['skin']);
        $smarty->assign('main', 'cleanup');
    }

    if (empty($action)) {
        $smarty->assign('main', 'domains');
        $domains = cw_md_get_domains();
        
        if (is_array($domains)) {
        	
        	foreach ($domains as $key => $domain) {
        		$domains[$key]['attributes'] = cw_func_call('cw_attributes_get', array('item_id' => $domain['domain_id'], 'item_type' => 'DM', 'language' => $edited_language));

        		if (is_array($domains[$key]['attributes'])) {
        	
		        	foreach ($domains[$key]['attributes'] as $akey => $attr) {
		        		$domains[$key]['attributes'][$akey]['fieldname'] = "posted_data[" . $domain['domain_id'] . "][attribute]";  		
		        	}
        		}
        	}
        }
        $smarty->assign('domains', $domains);
    }

    $location[] = array(cw_get_langvar_by_name('lbl_domains'), 'index.php?target='.$target);
    
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => 0, 'item_type' => 'DM', 'language' => $edited_language));

	if (is_array($attributes)) {
        	
		foreach ($attributes as $akey => $attr) {
			$attributes[$akey]['fieldname'] = "posted_data[0][attribute]";  		
		}
	}
    $smarty->assign('attributes', $attributes);
}
$smarty->assign('default_skin', $app_config_file['web']['skin']);
$smarty->assign('current_main_dir', 'addons/multi_domains');
$smarty->assign('current_section_dir', 'admin');

