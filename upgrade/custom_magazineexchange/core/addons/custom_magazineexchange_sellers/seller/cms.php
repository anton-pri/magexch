<?php
namespace cw\custom_magazineexchange_sellers;

$page_id = intval($request_prepared['page_id']);


// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

if ($action_function == 'cms_update') {
    cw_header_location('index.php?target=cms&mode=view&page_id='.$page_id);
}

$smarty->assign('current_main_dir',     'addons/' . addon_name);
$smarty->assign('current_section_dir',  'seller');
$smarty->assign('AltImagesDir', $app_web_dir . '/skins_magazineexchange/images');

return $action_result;

/* ================================================================================== */

/* Actions */

function cms() {
    return cms_list();
}

function cms_list() {
    global $smarty, $tables, $current_language, $customer_id;

    $query = "SELECT cms.contentsection_id, cms.active,
                    IF(ISNULL(l.name), cms.name, l.name) AS name
            FROM $tables[cms] AS cms 
            LEFT JOIN $tables[cms_alt_languages] AS l 
                ON cms.contentsection_id = l.contentsection_id AND l.code = '$current_language'
            INNER JOIN $tables[magazine_sellers_pages] as sp
                ON cms.contentsection_id = sp.contentsection_id AND sp.customer_id = '$customer_id'
            WHERE `type`='staticpage'
            ORDER BY name";
    
    $promopages = cw_query($query);

    $smarty->assign('promopages', $promopages);
    $smarty->assign('main',       'pages');
}

function cms_view() {
    global $smarty, $tables, $current_language, $customer_id, $request_prepared;
    
    $page_id = intval($request_prepared['page_id']);
    
    $query   = "SELECT 
                    cms.contentsection_id, 
                    cms.active,
                    IF(ISNULL(l.name), cms.name, l.name) AS name,
                    IF(ISNULL(l.content), cms.content, l.content) AS content
            FROM $tables[cms] AS cms 
            LEFT JOIN $tables[cms_alt_languages] AS l 
                ON cms.contentsection_id = l.contentsection_id AND l.code = '$current_language'
            INNER JOIN $tables[magazine_sellers_pages] as sp
                ON cms.contentsection_id = sp.contentsection_id AND sp.customer_id = '$customer_id'
            WHERE `type`='staticpage' AND cms.contentsection_id='$page_id'";
    
    $page    = cw_query_first($query);
    
    if (empty($page)) {
        return error('Access denied');
    }
    
    $smarty->assign('page', $page);
    $smarty->assign('main', 'page');
   
}

function cms_update() {
    global $tables, $current_language, $customer_id, $request_prepared, $config;

    $page_id = intval($request_prepared['page_id']);
    $page_id = cw_query_first_cell("SELECT contentsection_id FROM $tables[magazine_sellers_pages] WHERE contentsection_id='$page_id' AND customer_id = '$customer_id' LIMIT 1");
    
    if (empty($page_id)) {
        return error('Access denied');
    }
 //cw_var_dump($request_prepared);die();
    $data = array(
      'content' => htmlspecialchars_decode(trim($request_prepared['html_section_content']))
    );
    if ($current_language == $config['default_customer_language']) {
      cw_array2update('cms', $data, "contentsection_id = '".$page_id."'");
    }
    cw_array2update('cms_alt_languages', $data, "contentsection_id = '".$page_id."' AND code = '".$current_language."'");

    return true;
}
