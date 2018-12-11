<?php
global $tables, $smarty, $config;

cw_load('files',  'user');

$language = $_GET['language'];

$top_message = &cw_session_register('top_message', array());
$serverfile = &cw_session_register('serverfile');
$search_data = &cw_session_register('search_data', array());

$languages = cw_query("select ls.*, lng.value as language from $tables[languages_settings] as ls left join $tables[languages] as lng ON lng.code = '$current_language' and lng.name = CONCAT('language_', ls.code)");
$topics = cw_query_column("SELECT topic FROM $tables[languages] WHERE topic<>'' GROUP BY topic ORDER BY topic");

if ($action == 'update_filter') {
    $search_data['languages'] = $posted_data;
    cw_header_location("index.php?target=$target&language=$language");
}

if ($action == 'update_languages') {
    foreach($languages as $val) {
        $to_update = array(
	        'code' 			=> $val['code'],
	        'real_code' 	=> strtolower($val['code']),
	        'enable' 		=> (!empty($upd[$val['code']]['enable']) ? 1 : 0),
	        'charset'		=> $val['charset'],
	        'text_direction'=> (!empty($upd[$val['code']]['text_direction']) ? 1 : 0)
        );
        cw_array2insert('languages_settings', $to_update, true);
    }
    cw_header_location("index.php?target=$target");
}

if ($action == "update") {

	if ($var_value)
    foreach ($var_value as $key => $value)
        cw_array2update(
            'languages',
            array(
                'value' => $value['name'],
                'tooltip' => $value['tooltip']
            ),
            "code='$language' AND name='$key'"
        );

	$top_message = array(
		"content" => cw_get_langvar_by_name("lbl_lng_variable_updated")
	);


    cw_cache_clean('lang');
	$smarty->clear_all_cache();
	$smarty->clear_compiled_tpl();

	cw_header_location("index.php?target=languages&language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");
}

if ($action == "add") {
	if (empty($new_var_name)) {
		$top_message['content'] = cw_get_langvar_by_name("msg_err_empty_label");
		$top_message['type'] = "E";
		cw_header_location("index.php?target=languages&language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");

	} 
    elseif ($new_var_name != preg_replace('/[^A-Za-z0-9_]/', '', $new_var_name)) {
		$top_message['content'] = cw_get_langvar_by_name("msg_err_invalid_label");
		$top_message['type'] = "E";
		cw_header_location("index.php?target=languages&language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");
	}

	$topic = in_array($new_topic, $topics) ? $new_topic : $topics[0];

	$is_exists = cw_query_first_cell("select count(*) from $tables[languages] WHERE name = '$new_var_name' AND code='$language'") > 0;
	if ($is_exists) {
		cw_array2update("languages", 
			array(
				'value' => $new_var_value,
				'tooltip' => $new_var_tooltip
			),
			"name='$new_var_name' AND code='$language'"
		);
	} 
    else {
		foreach ($languages as $key=>$value) {
			cw_array2insert("languages", 
				array(
					"code" => $value['code'],
					"name" => $new_var_name,
					"value" => $new_var_value,
					"tooltip" => $new_var_tooltip,
					"topic" => $topic
				),
				true
			);
		}
	}

	$top_message = array(
		"content" => cw_get_langvar_by_name("lbl_lng_variable_added")
	);

	cw_header_location("index.php?target=languages&language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");
}
elseif ($action == "delete" && !empty($ids)) {

	db_query ("DELETE FROM $tables[languages] WHERE name IN ('".implode("','", $ids)."')");

	$top_message = array(
		"content" => cw_get_langvar_by_name("lbl_lng_variables_deleted")
	);

	cw_header_location("index.php?target=languages&language=$language&page=$page&filter=".urlencode($filter)."&topic=$topic");
}
elseif ($action == "del_lang") {

	if (is_array($del)) {

		foreach ($del as $language => $delete) {
			db_query("DELETE FROM $tables[languages] WHERE code='$language'");
			db_query("DELETE FROM $tables[languages_settings] WHERE code='$language'");
			db_query("DELETE FROM $tables[attributes_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[attributes_default_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[categories_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[manufacturers_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[memberships_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[payment_methods_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[products_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[register_fields_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[register_fields_sections_lng] WHERE code='$language'");
			db_query("DELETE FROM $tables[speed_bar_lng] WHERE code='$language'");
		}

		$top_message = array(
			"content" => cw_get_langvar_by_name("lbl_languages_has_been_deleted")
		);
	}

	cw_header_location("index.php?target=languages");
}
elseif ($action == "export" && $language) {
    $delimiter = ';';
	$smarty->assign ("csv_delimiter", $delimiter);

	$lng_res = cw_query_first_cell ("SELECT value FROM $tables[languages] WHERE name='language_$language'");

	$data = cw_query ("SELECT * FROM $tables[languages] WHERE code='$language' ORDER BY name");
	if ($data) {
		foreach ($data as $key => $value) {
			$data[$key]['value'] = "\"" . eregi_replace ("\"", "\"\"", $value['value']) . "\"";
		}

		$smarty->assign ("data", $data);

		header ("Content-Type: text/csv");
		header ("Content-Disposition: attachment; filename=lng_".$lng_res.".csv");

		$_tmp_smarty_debug = $smarty->debugging;
		$smarty->debugging = false;

		cw_display("main/lng_export.tpl",$smarty);

		$smarty->debugging = $_tmp_smarty_debug;
		exit;
	}
}

if ($action == "add_lang") {

	if (!$new_language)
		cw_header_location("index.php?target=languages");

	$exists_result = cw_query_first ("SELECT * FROM $tables[languages] WHERE code='$new_language'");

    if (!cw_query_first_cell("select count(*) from $tables[languages_settings] where code='$new_language'"))
        cw_array2insert('languages_settings', array('code' => $new_language));

	if (!$exists_result) {
		$result = cw_query("select * from $tables[languages] where code='$config[default_customer_language]'");
		if ($result)
		foreach ($result as $key=>$value) {
		    db_query ("INSERT INTO $tables[languages] (code, name, value, topic) VALUES ('$new_language', '".addslashes($value['name'])."','".addslashes($value['value'])."','$value[topic]')");
		}

	    $lngs = cw_query_column("SELECT code FROM $tables[languages] GROUP BY code");
	}

	if ($source == "server" && !empty($localfile)) {
		# File is located on the server
		$localfile = stripslashes($localfile);
		if (cw_allow_file($localfile, true) && is_file($localfile)) {
			$import_file = $localfile;
			$is_import = true;
		} else {
			$top_message['content'] = cw_get_langvar_by_name("msg_err_file_wrong");
			$top_message['type'] = "E";
			$serverfile = $localfile;
			cw_header_location("index.php?target=languages");
		}
	} elseif ($source == "upload" && $import_file && $import_file != "none") {
		$import_file = cw_move_uploaded_file("import_file");
		$is_import = true;
	} else {
		$is_import = false;
	}
	if ($is_import) {
		if ($fp = cw_fopen($import_file, "r", true)) {
			$lngs = $avail_languages;
			while ($columns = fgetcsv ($fp, 65536, $delimiter)) {
				if (sizeof($columns) >= 4) {
					$res = cw_query_first ("SELECT * FROM $tables[languages] WHERE name='$columns[0]' AND $tables[languages].code = '$new_language' LIMIT 1");
					if ($res) {
						db_query ("UPDATE $tables[languages] SET value='".addslashes($columns[1])."', topic='".addslashes($columns[3])."' WHERE name='$columns[0]' AND code='$new_language'");
					} else {
						db_query ("INSERT INTO $tables[languages] (code, name, value, topic) VALUES ('$new_language','$columns[0]','".addslashes ($columns[1])."','".addslashes ($columns[3])."')");
					}
				}
			}
			fclose ($fp);
		}
	}

	cw_header_location("index.php?target=languages&language=$new_language&topic=$topic&page=$page");
}

if ($action == "change_defaults") {
	if (!empty($new_customer_language))
		db_query("update $tables[config] set value='$new_customer_language' where name='default_customer_language'");
	if (!empty($new_admin_language))
		db_query("update $tables[config] set value='$new_admin_language' where name='default_admin_language'");

	cw_header_location("index.php?target=languages&language=$language");
}

if ($language) {
    $language_info = cw_query_hash("select ls.*, lng.value as language from $tables[languages_settings] as ls left join $tables[languages] as lng ON lng.code = '$current_language' and lng.name = CONCAT('language_', ls.code) where ls.code='$language'");
	$smarty->assign("language_info", $language_info);

    $data = $search_data['languages'];
    $conditions = array();
	if ($data['topic'])
		$conditions[] = "lng.topic='$data[topic]'";
	else
		$conditions[] = "lng.topic<>''";

	if ($data['filter'])
		$conditions[] = "(lng.name LIKE '%$data[filter]%' or lng.value LIKE '%$data[filter]%')";

    if ($data['not_translated'])	
        $conditions[] = "lng.value = lng_e.value";

	$query = "select lng.* from $tables[languages] as lng left join $tables[languages] as lng_e on lng.name=lng_e.name and lng_e.code='EN' where lng.code='$language' and ".implode(' and ', $conditions)." order by lng.topic, lng.name";

	$result = db_query($query);
	$total_labels_in_search = db_num_rows($result);
    $navigation = cw_core_get_navigation($target, $total_labels_in_search, $page);
    $navigation['script'] = "index.php?target=$target&language=$language"; 
    $smarty->assign('navigation', $navigation);

	if ($total_labels_in_search > 0)
		$smarty->assign("data", cw_query ("$query LIMIT $navigation[first_page], $navigation[objects_per_page]"));
}

$smarty->assign("upload_max_filesize", ini_get("upload_max_filesize"));
$smarty->assign("my_files_location",cw_user_get_files_location());
if (!empty($serverfile)) {
	$smarty->assign ("localfile", $serverfile);
	$serverfile = false;
}
else
	$smarty->assign("localfile", cw_user_get_files_location()."/lng_file.csv");

if ($language) {
    $smarty->assign('topics', $topics);

    $smarty->assign('search_prefilled', cw_array_map('stripslashes', $search_data['languages']));
    $smarty->assign('language', $language);

    $location[] = array(cw_get_langvar_by_name('lbl_edit_languages'), 'index.php?target='.$target);
    $location[] = array(cw_get_langvar_by_name('lbl_edit_language'), '');
    $smarty->assign('main', 'language');
}
else {
    $new_languages = cw_query ("SELECT $tables[map_countries].*, IFNULL(lng1c.value, lng2c.value) as country, IFNULL(lng1l.value, lng2l.value) as language FROM $tables[map_countries] LEFT JOIN $tables[languages] as lng1c ON lng1c.name = CONCAT('country_', $tables[map_countries].code) AND lng1c.code = '$current_language' LEFT JOIN $tables[languages] as lng2c ON lng2c.name = CONCAT('country_', $tables[map_countries].code) AND lng2c.code = '$config[default_admin_language]' LEFT JOIN $tables[languages] as lng1l ON lng1l.name = CONCAT('language_', $tables[map_countries].code) AND lng1l.code = '$current_language' LEFT JOIN $tables[languages] as lng2l ON lng2l.name = CONCAT('language_', $tables[map_countries].code) AND lng2l.code = '$config[default_admin_language]' WHERE (lng1l.value != '' OR lng2l.value != '') GROUP BY language ORDER BY language");
    $smarty->assign ("new_languages", $new_languages);

    $smarty->assign('languages', $languages);

    $location[] = array(cw_get_langvar_by_name('lbl_edit_languages'), '');
    $smarty->assign('main', 'languages');
}
