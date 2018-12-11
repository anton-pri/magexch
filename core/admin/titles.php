<?php


# Add title
if ($action == "add" && !empty($add['title'])) {
	if (empty($add['orderby']))
		$add['orderby'] = cw_query_first_cell("SELECT MAX(orderby) FROM $tables[titles]")+1;
	cw_languages_alt_insert("title_".$id, $v['title'], $current_language);
	cw_array2insert("titles", $add);

# Update title(s)
} elseif ($action == "update" && !empty($data)) {
	foreach ($data as $id => $v) {
		$v['active'] = $v['active'];
		cw_languages_alt_insert("title_".$id, $v['title'], $current_language);
		if ($current_language != $config['default_admin_language'])
			unset($v['title']);
		cw_array2update("titles", $v, "titleid = '$id'");
	}

# Delete title(s)
} elseif ($action == "delete" && !empty($ids)) {
	$string = "titleid IN ('".implode("','", $ids)."')";
	db_query("DELETE FROM $tables[titles] WHERE ".$string);
	db_query("DELETE FROM $tables[languages_alt] WHERE name IN ('title_".implode("','title_", $ids)."')");
}

if (!empty($action)) {
	cw_header_location("index.php?target=titles");
}

$titles = cw_query("SELECT * FROM $tables[titles] ORDER BY orderby, title");
if (!empty($titles)) {
	foreach ($titles as $k => $v) {
		$name = cw_get_languages_alt("title_".$v['titleid']);
		if (!empty($name))
			$titles[$k]['title'] = $name;
	}
	$smarty->assign('titles', $titles);
}

$smarty->assign('main', 'titles');
