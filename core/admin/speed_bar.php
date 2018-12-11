<?php
cw_load('speed_bar', 'attributes');

$top_message = &cw_session_register('top_message', array());
$bar_modified_data = &cw_session_register('bar_modified_data', array());

if ($action == 'update') {
    foreach($update_speed_bar as $k=>$v) {
        if ($v['del']) {
            cw_speed_bar_delete($k);
            continue;
        }
        $v['item_id'] = $k;
        if ($v['item_id'])
            cw_array2update('speed_bar', $v, "item_id='$v[item_id]'", array('active', 'orderby'));
    }
    cw_header_location("index.php?target=$target");
}

if ($action == 'update_one') {

    $rules = array(
        'link' => '',
        'title' => '',
    );
    $update_speed_bar['item_id'] = $item_id;
    $update_speed_bar['attributes'] = $attributes;
	$fillerror = cw_error_check($update_speed_bar, $rules, 'B');

    if (!$fillerror) {
        if (!$item_id) {
            $update_speed_bar['item_id'] = $item_id = cw_array2insert('speed_bar', $update_speed_bar, 1, array('link', 'title', 'active', 'orderby'));
        }

        cw_array2update('speed_bar', $update_speed_bar, "item_id='$item_id'", array('link', 'title', 'active', 'orderby'));
        $update_speed_bar['code'] = $edited_language;
        cw_array2insert('speed_bar_lng', $update_speed_bar, 1, array('item_id', 'code', 'title'));
        cw_call('cw_attributes_save', array('item_id' => $item_id, 'item_type' => 'B', 'attributes' => $attributes, 'language' => $edited_language));
    }
    else {
		$top_message = array('content' => $fillerror, 'type' => 'E');
		$bar_modified_data = $update_speed_bar;
    }
    cw_header_location("index.php?target=$target&speed_id=$item_id");
}

if (isset($speed_id)) {
    $bar = cw_speed_bar_get_one($speed_id, $edited_language);
    if ($bar_modified_data) $bar = array_merge($bar, $bar_modified_data);
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $speed_id, 'item_type' => 'B', 'prefilled' => $bar_modified_data['attributes'], 'language' => $edited_language));
    $bar_modified_data = '';

    $smarty->assign('bar', $bar);
    $smarty->assign('attributes', $attributes);
    $smarty->assign('mode', 'modify');
}
else {
    $speed_bar = cw_func_call('cw_speed_bar_search', array('language' => $edited_language));
    $smarty->assign('speed_bar', $speed_bar);
}

$location[] = array(cw_get_langvar_by_name('lbl_speed_bar'), 'index.php?target='.$target);
$smarty->assign('main', 'speed_bar');
