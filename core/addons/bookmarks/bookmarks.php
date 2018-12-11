<?php
namespace CW\bookmarks;

cw_load('ajax');
if (empty($action)) $action='get';

if ($action=='add') {
    if ($customer_id) $bookmarks = cw_query_first_cell("SELECT count(*) FROM $tables[bookmarks] WHERE customer_id='".intval($customer_id)."' AND url='$_GET[url]'");
    else $bookmarks = cw_query_first_cell("SELECT count(*) FROM $tables[bookmarks] WHERE sess_id='$APP_SESS_ID-".AREA_TYPE."' AND url='$_GET[url]'");


    $name = preg_replace('/(\s-\s)*'.$location[0][0].'(\s-\s)*/','',$name);

    $breadcrumbs = explode('-', $name);
    $name = trim(array_pop($breadcrumbs));

    if ($bookmarks == 0) {
        $data = array(
            'customer_id' => $customer_id,
            'sess_id' => $APP_SESS_ID.'-'.AREA_TYPE,
            'url' => $_GET['url'],
            'name' =>$_GET['name'],
            'pos' => 0
        );
        cw_array2insert('bookmarks',$data);
    }

    $action='get';
}

if ($action == 'delete') {
    if ($customer_id) $bookmarks = db_query("DELETE FROM $tables[bookmarks] WHERE customer_id='".intval($customer_id)."' AND md5(url)='$_GET[id]'");
    else $bookmarks = db_query("DELETE FROM $tables[bookmarks] WHERE sess_id='$APP_SESS_ID-".AREA_TYPE."' AND md5(url)='$_GET[id]'");

    $action='get';
}

if ($action == 'get') {
    if ($customer_id) $bookmarks = get_by_customer($customer_id);
    else $bookmarks = get_by_session($APP_SESS_ID.'-'.AREA_TYPE);

    $smarty->assign('bookmarks', $bookmarks);

    cw_add_ajax_block(array(
        'id' =>'bm_content',
        'template' => 'addons/'.addon_name.'/panel.tpl'
    ));
}
