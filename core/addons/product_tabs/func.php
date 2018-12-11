<?php

if (!defined('APP_START')) { die('The software application has not been initialized.'); }


function cw_pt_tabs_comparison($tab1, $tab2) {

	if (!isset($tab1['number']) || !isset($tab2['number'])) {
		return 0;
	}

	if ($tab1['number'] == $tab2['number']) {
		if (!isset($tab1['global']) && isset($tab2['global'])) {
			return 1;
		} elseif (isset($tab1['global']) && !isset($tab2['global'])) {
			return -1;
		} else {
			return 0;
		}
	}

	return ($tab1['number'] < $tab2['number']) ? -1 : 1;
}


function cw_pt_get_tab_content($param) {
	global $pt_tabs;
	static $tab_number; 
	
	if (!isset($pt_tabs)) {
		return null;
	}
	
	if (!isset($tab_number)) {
		$tab_number = 0;
	} else {
		$tab_number++;
	}
	
	return isset($pt_tabs[$tab_number]) ? $pt_tabs[$tab_number] : null;
}

?>