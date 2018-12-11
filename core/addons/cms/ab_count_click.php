<?php

$cms_ids_clicked = &cw_session_register('cms_ids_clicked', array());

$contentsection_id = intval($contentsection_id);

if (empty($contentsection_id)) exit;

if (in_array($contentsection_id, $cms_ids_clicked)) exit; // Ignore further clicks to the same banner within a session

$query = "SELECT contentsection_id FROM $tables[cms_user_counters] WHERE contentsection_id = '$contentsection_id'";
$exists = intval(cw_query_first_cell($query));
if ($exists) {
    db_query("UPDATE $tables[cms_user_counters] SET clicked=clicked+1 WHERE contentsection_id = '$contentsection_id'");
}
else {
    cw_array2insert('cms_user_counters', array('contentsection_id'=>$contentsection_id,'count'=>1,'clicked' => '1'), true);
}
$cms_ids_clicked[] = $contentsection_id;

exit;
