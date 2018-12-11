<?php

global $current_location;

$result = cw_clean_url_delete_url_by_id_and_get_data($url_id);

$content = "No urls";
$url_list = cw_clean_url_get_url_list_from_history($result['item_id'], $result['item_type']);

if (count($url_list)) {
	$content = "";

	foreach ($url_list as $url) {
		$content .= $url['url'] . '&nbsp;';
		$content .= '<a href="javascript:delete_clean_url(\'' . $url['id'] . '\');">';
		$content .= '<img src="' . $current_location . '/skins/images/delete_cross.gif" alt="Delete from history" title="Delete from history">';
		$content .= '</a><br>';
	}
}

cw_add_ajax_block(array(
	'id' 		=> 'clean_urls_container',
	'action' 	=> 'update',
	'content' 	=> $content
));
