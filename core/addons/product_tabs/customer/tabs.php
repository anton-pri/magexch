<?php

if (!isset($addons['product_tabs'])) return;
if (!isset($product_id) || empty($product_id)) return;

	
	require_once $app_main_dir . '/addons/product_tabs/func.php';
	
	
	$product_id = (int) $product_id;

	global $pt_tabs, $pt_is_tabs;
	$pt_is_tabs = false;

    $pt_tabs = cw_query('SELECT `title`, `content`, `parse`, `number`, `attributes` FROM ' . $tables[$_pt_addon_tables['product']] . ' WHERE product_id = \'' . $product_id . '\' AND `active` = 1 AND (`content` != \'\' OR `content` != \'<p>&#160;</p>\') ORDER BY `number`, `title`');

    if (empty($pt_tabs) || !is_array($pt_tabs)) {
    	$pt_tabs = array();
    }

    $global_tabs = cw_query('SELECT `title`, `content`, `parse`, `number`, `attributes`, 1 as global FROM ' . $tables[$_pt_addon_tables['global']] . ' WHERE `active` = 1 AND (`content` != \'\' OR `content` != \'<p>&#160;</p>\') ORDER BY `number`, `title`');

    if (empty($global_tabs) || !is_array($global_tabs)) {
    	$global_tabs = array();
    }

    $pt_tabs = array_merge($global_tabs, $pt_tabs);

    if (empty($pt_tabs) || !is_array($pt_tabs)) return;

    usort($pt_tabs, 'cw_pt_tabs_comparison');

    foreach ($pt_tabs as $tab_key => $tab) {
        
    	if (!isset($tab['content']) || !isset($tab['title'])) {
            unset($pt_tabs[$tab_key]);
            continue;
    	}
    	
    	$tab['content'] = trim($tab['content']);
    	$tab['title'] = trim($tab['title']);

        if (empty($tab['title']) || empty($tab['content'])) {
            unset($pt_tabs[$tab_key]);
            continue;
    	}

		if (in_array($tab['content'], array('<p>&#160;</p>', '<p></p>', '&#160;'))) {
			unset($pt_tabs[$tab_key]);
			continue;
		}
		
    	$pt_tabs[$tab_key]['content'] = $tab['content'];
    	$pt_tabs[$tab_key]['title'] = $tab['title'];
    	$pt_tabs[$tab_key]['attributes'] = unserialize($tab['attributes']);
    }
    
    /**
     * Tab content will be taken/displayed via cw_pt_get_tab_content function 
     * from the $pt_tabs global array in skins/addons/product_tabs/customer/tab_content.tpl template 
     */
    
    if (!empty($pt_tabs)) 
    	$pt_is_tabs = true;
