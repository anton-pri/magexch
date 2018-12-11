<?php
# kornev, TOFIX
if (!$addons['Salesman'])
    cw_header_location('index.php');

if ($action == "edit" && $level) {
	foreach ($level as $k => $v) {
		$v = cw_convert_number($v);
		db_query("REPLACE INTO $tables[salesman_tier_commissions] VALUES ('$k', '$v')");
	}
	cw_header_location("index.php?target=salesman_level_commissions");
}

$levels = array();
for($x = 1; $x < $config['Salesman']['salesman_max_level']; $x++)
	$levels[$x] = cw_query_first("SELECT * FROM $tables[salesman_tier_commissions] WHERE level = '$x'");

db_query("DELETE FROM $tables[salesman_tier_commissions] WHERE level > '".($config['Salesman']['salesman_max_level']-1)."'");

$smarty->assign('levels', $levels);

$smarty->assign('main', 'level_commissions');
