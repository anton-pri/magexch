<?php
cw_load( 'image', 'attributes');

$file_upload_data = &cw_session_register('file_upload_data');
$search_data = &cw_session_register('search_data', array());
$saved_manufacturer = &cw_session_register('saved_manufacturer', array());
cw_image_clear(array('manufacturer_images'));

$top_message = &cw_session_register('top_message', array());

if ($action == 'search') {
    $search_data['manufacturers']['substring'] = $posted_data['substring'];
    cw_header_location('index.php?target='.$target);    
}

if ($action == 'details') {
    $rules = array(
        'manufacturer' => '',
    );
    $manufacturer_update['attributes'] = $attributes;
    $fillerror = cw_error_check($manufacturer_update, $rules, 'M');
    if ($fillerror) {
        $top_message = array(
            'content' => $fillerror,
            'type' => 'E'
        );
        $saved_manufacturer = $manufacturer_update;
        cw_header_location("index.php?target=$target&".($manufacturer_id?"manufacturer_id=$manufacturer_id":'mode=add'));
    }

    $to_update = array(
        'manufacturer' => $manufacturer_update['manufacturer'],
        'url' => $manufacturer_update['url'],
		'descr' => $manufacturer_update['descr'],
        'featured' => $manufacturer_update['featured'],
        'avail' => $manufacturer_update['avail'],
        'orderby' => $manufacturer_update['orderby'],
        'show_image' => $manufacturer_update['show_image'],
    );
    $to_update_lng = array(
	    'manufacturer_id' => $manufacturer_id,
		'code' => $edited_language,
		'descr' => $manufacturer_update['descr'],
        'manufacturer' => $manufacturer_update['manufacturer']
    );
    if ($edited_language != $config['default_admin_language'] && $manufacturer_id)
        cw_unset($to_update, 'manufacturer', 'descr');

    if (!$manufacturer_id) {
        $top_message['content'] = cw_get_langvar_by_name("msg_adm_err_manufacturer_add");
        $manufacturer_id = cw_array2insert('manufacturers', $to_update);
        $to_update_lng['manufacturer_id'] = $manufacturer_id;
    }
    else
        $top_message['content'] = cw_get_langvar_by_name("msg_adm_err_manufacturer_upd");

    cw_array2update('manufacturers', $to_update, "manufacturer_id='$manufacturer_id' ".$warehouse_condition);
	cw_array2insert('manufacturers_lng', $to_update_lng, true);

    cw_call('cw_attributes_save', array('item_id' => $manufacturer_id, 'item_type' => 'M', 'attributes' => $attributes, 'language' => $edited_language));

    if (cw_image_check_posted($file_upload_data['manufacturer_images']))
        cw_image_save($file_upload_data['manufacturer_images'], array('id' => $manufacturer_id));
        
    cw_cache_clean('manufacturers_all');
        
    cw_header_location("index.php?target=$target&manufacturer_id=$manufacturer_id&page=$page");
}

if ($action == "delete" and !empty($to_delete) && is_array($to_delete)) {
    foreach($to_delete as $manufacturer_id=>$tmp)
        cw_call('cw_manufacturer_delete', array($manufacturer_id));
    $top_message['content'] = cw_get_langvar_by_name("msg_adm_manufacturer_del");
    cw_cache_clean('manufacturers_all');
    cw_header_location("index.php?target=$target&page=$page");
}

if ($action == 'delete_image' && $manufacturer_id) {
    cw_image_delete($manufacturer_id, 'manufacturer_images');
    cw_header_location("index.php?target=$target&manufacturer_id=$manufacturer_id");
}

if ($action == "update") {
    if (is_array($records))
    foreach ($records as $k=>$v) {
        $v['avail'] = $v['avail']?1:0;
        $v['featured'] = $v['featured']?1:0;

        db_query($sql="UPDATE $tables[manufacturers] SET avail='$v[avail]', orderby='$v[orderby]', featured='$v[featured]' WHERE manufacturer_id='$k' $warehouse_condition");
	}
	$top_message['content'] = cw_get_langvar_by_name("msg_adm_manufacturers_upd");
    cw_header_location("index.php?target=$target&manufacturer_id=$manufacturer_id&page=$page");
}


if ($manufacturer_id) {
    $manufacturer_data = cw_func_call('cw_manufacturer_get', array('manufacturer_id' => $manufacturer_id));

    if (empty($manufacturer_data)) {
        $top_message = array('type' => 'E', 'content' => cw_get_langvar_by_name('msg_adm_err_manufacturer_not_exists'));
		cw_header_location('index.php?target='.$target);
    }
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $manufacturer_id, 'item_type' => 'M', 'prefilled' => $saved_manufacturer['attributes'], 'language' => $edited_language));
    if ($saved_manufacturer) {
        $manufacturer_data = array_merge($manufacturer_data, $saved_manufacturer);
        $saved_manufacturer = null;
    }

    $smarty->assign('manufacturer', $manufacturer_data);
	$smarty->assign('mode', 'manufacturer_info');

    $location[] = array(cw_get_langvar_by_name('lbl_manufacturers'), '');
    $location[] = array(cw_get_langvar_by_name('lbl_manufacturer'), '');
    $location[] = array($manufacturer_data['manufacturer'], '');

    $smarty->assign('attributes', $attributes);

}
elseif($mode == 'add') {
    $location[] = array(cw_get_langvar_by_name('lbl_manufacturers'), '');
    $location[] = array(cw_get_langvar_by_name('lbl_add_manufacturer'), '');

    $attributes = cw_func_call('cw_attributes_get', array('item_id' => 0, 'item_type' => 'm', 'prefilled' => $saved_manufacturer['attributes'], 'language' => $edited_language));
    $manufacturer_data = array();
    if ($saved_manufacturer) {
        $manufacturer_data = $saved_manufacturer;
        $saved_manufacturer = null;
    }

    $smarty->assign('manufacturer', $manufacturer_data);
    $smarty->assign('attributes', $attributes);
    $smarty->assign('mode', 'manufacturer_info');
}
else {
    if ($sort)
        $search_data['manufacturers']['sort_field'] = $sort;
    if (isset($sort_direction))
        $search_data['manufacturers']['sort_direction'] = $sort_direction;
    if (!$search_data['manufacturers']['sort_field']) 
        $search_data['manufacturers']['sort_field'] = 'manufacturer';

    $search_data['manufacturers']['page'] = $page;
    list($manufacturers, $navigation) = cw_func_call('cw_manufacturer_search', array('data' => $search_data['manufacturers'], 'info_type' => ($config['manufacturers']['manufacturers_show_cnt_admin']=='Y'?2:1)));
    $navigation['script'] = 'index.php?target='.$target;

    $smarty->assign('navigation', $navigation);
    $smarty->assign('manufacturers', $manufacturers);
    $smarty->assign('search_prefilled', $search_data['manufacturers']);

    $location[] = array(cw_get_langvar_by_name('lbl_manufacturers'), '');
}

$smarty->assign('main', 'manufacturers');
