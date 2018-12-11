<?php
$location[] = array(cw_get_langvar_by_name("lbl_edit_cc_types"), "");

function cw_local_update_card_types () {
	global $config, $tables;

	db_query ("UPDATE $tables[config] SET value='".addslashes(serialize($config['card_types']))."' WHERE name='card_types'");
}

$test = cw_query_first ("SELECT * FROM $tables[config] WHERE name='card_types'");
if (!$test) {
	db_query ("INSERT INTO $tables[config] (name, value) VALUES ('card_types', '')");
}


if ($REQUEST_METHOD == "POST") {

	if (isset($new_name))
		$new_name = trim($new_name);
	if (isset($code))
		$code = trim($code);

	if ($action == "add" && !empty($code) && !empty($new_name)) {
	#
	# Add a new credit card type
	#
		$config['card_types'][] = 
                    array ("code" => stripslashes($code), 
                           "type" => stripslashes($new_name), 
                           "cvv2" => (!empty($new_cvv2) ? "1" : ""),
                           "active" => (!empty($new_active) ? "1" : "0") 
                    );

		cw_local_update_card_types();

		$top_message['content'] = cw_get_langvar_by_name("msg_adm_card_types_add");
	
	}
	elseif ($action == "delete" and !empty($posted_data)) {
	#
	# Delete selected credit card types
	#
		if (is_array($posted_data) && is_array($config['card_types'])) {
			$deleted = false;
			$new_levels = array();
			foreach ($config['card_types'] as $key=>$value) {
				foreach ($posted_data as $k=>$v) {
					if ($value['code'] == stripslashes($v['code']) && $value['type'] == stripslashes($v['old_name'])) {
						if (empty($v['to_delete']))
							$new_levels[] = $value;
						else
							$deleted = true;
						break;
					}
				}
			}

			if ($deleted) {
				$config['card_types'] = $new_levels;
				cw_local_update_card_types();
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_card_types_del");
			}
		}

		
	}
	elseif ($action == "update" && !empty($posted_data)) {
	#
	# Update credit card types list
	#
		if (is_array($posted_data) && is_array($config['card_types'])) {
			$updated = false;
			foreach ($config['card_types'] as $key=>$value) {
				foreach ($posted_data as $k=>$v) {
				
					$v['new_name'] = trim($v['new_name']);
					if (empty($v['new_name']))
						continue;
					
					$need_to_update =
						($value['code'] == stripslashes($v['code']) && 
						$value['type'] == stripslashes($v['old_name']) && 
						($value['type'] != $v['new_name'] || 
						(empty($value['cvv2']) + empty($v['new_cvv2'])) ||
                                                (empty($value['active']) + empty($v['new_active']))));
					
					if ($need_to_update) {
						$config['card_types'][$key]['type'] = stripslashes($v['new_name']);
						$config['card_types'][$key]['cvv2'] = (!empty($v['new_cvv2']) ? "1" : "");
                                                $config['card_types'][$key]['active'] = (!empty($v['new_active']) ? "1" : "0");
						$updated = true;
						break;
					}
				}
			}

			if ($updated) {
				cw_local_update_card_types();
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_card_types_upd");
			}
		}
	}
	
	cw_header_location("index.php?target=card_types");

}

$smarty->assign('main', "card_types");
