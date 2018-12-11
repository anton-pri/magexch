<?php
global $REQUEST_URI, $REQUEST_METHOD;

if (!defined('IS_AJAX')) {
    $url = parse_url($REQUEST_URI);

    if (
        isset($url['path'])
        && strpos($url['path'], 'index.php') !== FALSE
    ) {
        $test_link = '/index.php';

        if (isset($url['query'])) {
            $test_link .= '?' . preg_replace('/[0-9]+/', '[[ANY]]', $url['query']);
        }
        $breadcrumbs = cw_breadcrumbs_get_breadcrumbs($test_link, $url['query']);

        if (!empty($breadcrumbs)) {
            global $tables, $current_language, $config, $smarty;

            $lang = !empty($current_language) ? $current_language : $config['default_customer_language'];

            if (count($breadcrumbs) == 1) {
                $query = "
                    SELECT value
                    FROM $tables[languages]
                    WHERE code = '$lang' and name = '" . $breadcrumbs[0]['title'] . "'
                ";

                if (cw_query_first_cell($query)) {
                    $breadcrumbs[0]['title'] = cw_get_langvar_by_name($breadcrumbs[0]['title'],null,false,true,true,true);
                }
            }
            else {
                for ($bkey = 0; $bkey < count($breadcrumbs); $bkey++) {
                    $query = "
                        SELECT value
                        FROM $tables[languages]
                        WHERE code = '$lang' and name = '" . $breadcrumbs[$bkey]['title'] . "'
                    ";

                    if (cw_query_first_cell($query)) {
                        $breadcrumbs[$bkey]['title'] = cw_get_langvar_by_name($breadcrumbs[$bkey]['title'],null,false,true,true,true);
                    }
                }
            }
        }
        else {
            $breadcrumbs = array();

            if (!empty($test_link) && cw_breadcrumbs_is_unique_link($test_link, 'add_breadcrumb', 0)) {
                cw_array2insert(
                    'breadcrumbs',
                    array(
                        'link' => $test_link,
                        'title' => 'Unknown',
                        'parent_id' => -1,
                        'area'  => APP_AREA
                    )
                );
            }
        }
        
        array_unshift($breadcrumbs, array('link' => '/index.php', 'title' => cw_get_langvar_by_name('lbl_area_'.APP_AREA)));
        
        $smarty->assign('location_breadcrumbs', $breadcrumbs);
        $last_breadcrumb = array_pop($breadcrumbs);
        $smarty->assign('title_breadcrumb', $last_breadcrumb['title']);
    }
}
