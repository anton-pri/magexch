<?php
if (APP_AREA == 'customer') {
    global $tables, $current_language, $config, $REQUEST_METHOD, $current_location, $app_web_dir;

    global $current_page_clean_url;

    cw_load('attributes');

    $lang = !empty($current_language) ? $current_language : $config['default_customer_language'];

    $params_query = array(
        'product'       => array('product_id', 'P'),
        'index'         => array('cat', 'C'),
        'manufacturers' => array('manufacturer_id', 'M'),
        'pages'         => array('page_id', 'AB'),
        'search'        => array('mode', 'Q')
    );

# kornev, check the clean url
    $url = parse_url($REQUEST_URI);
    $clean_url = substr($url['path'], strlen($app_web_dir));

    $clean_url = trim($clean_url, '/');

    $current_page_clean_url = $clean_url;

    // get clean url and "find" dynamic url
    if (
        $clean_url
        && strpos($clean_url, 'index.php') === FALSE
//        && empty($_GET['att'])
    ) {
        
        // Redirect to normalized URL
        // TODO: Optimize. 
        // cw_clean_url_get_seo_url - parses URL to full dynamic and then collapse back to fully SEO-friendly
        // So it makes all same parses as in routine below
        $current_url = trim($clean_url.'?'.$url['query'],'/?');
        $normalized_url = trim(cw_clean_url_get_seo_url($current_url),'/?');
        if (urldecode($current_url) != urldecode($normalized_url) && !empty($normalized_url)) {
            //cw_var_dump($current_url,$normalized_url);
            cw_header_location($current_location . '/' . $normalized_url, true, false, 301);
        }
        // /TODO
        
        
        $clean_urls = explode('/', $clean_url);

        // if some options combination is a custom facet url
        // e.g. search/attribute-A/attribute-B --[redirect]--> search/custom-url-for-A-B
        if ($combination_url = cw_call('cw_clean_url_get_custom_facet_url_from_combination', array($clean_urls))) {
            $search_data = &cw_session_register("search_data", array());
            $search_data['products']['customer_search']['redirected_to_facet'] = true;
            cw_header_location($current_location . '/' . $combination_url.'?'.$url['query']);
        }

        // Check custom facet url in urls
        // e.g. search/custom-url-for-A-B --[parse as]--> search/attribute-A/attribute-B
        $new_clean_urls = array();
        $converted_clean_urls = array();
        foreach ($clean_urls as $urk_key => $clean_url) {
            if ($custom_facet_url = cw_clean_url_get_custom_facet_url($clean_url)) {
                define('FACET_URL', $clean_url);
                $custom_facet_clean_url_parts = explode('/', $custom_facet_url['clean_urls']);
                $new_clean_urls = array_merge($new_clean_urls, $custom_facet_clean_url_parts);
                $converted_clean_urls = array_merge($converted_clean_urls, $custom_facet_clean_url_parts);
                cw_load('image');
                $smarty->assign('facet_category', array('image'=>cw_image_get('facet_categories_images', $custom_facet_url['url_id'])));
            } else {
                $new_clean_urls[] = $clean_url;
            }
        }
        $clean_urls = $new_clean_urls;

        $clean_url_attrubute_ids = array_keys(cw_call('cw_attributes_filter', array(array('field'=>'clean_url'))));
        $clean_url_html_attribute_ids = cw_call('cw_clean_url_html_attribute_ids', array());
        foreach ($clean_urls as $urk_key => $clean_url) {
            // Detect data
            $clean_url_values = array();    // values for range or select attributes
            $clean_url_parts = explode('-', $clean_url);

            // for substring use special code
            if ($clean_url_parts[0] == 'substring') {
                global $att;
                unset($clean_url_parts[0]);
                $_GET['att']['substring'] = implode('-', $clean_url_parts);
                $att['substring'] = implode('-', $clean_url_parts);
            }
            else {
                $url2search = "";
                do {
                    $url2search = addslashes(implode('-', $clean_url_parts));
                    $clean_url_html_sql_lookup = '';
                    $url2search_no_html = '';

                    if (!empty($clean_url_html_attribute_ids)) {
                        if (strtolower(substr($url2search, -5)) == ".html") 
                            $url2search_no_html = substr($url2search, 0, -5);

                        if (strtolower(substr($url2search, -4)) == ".htm") 
                            $url2search_no_html = substr($url2search, 0, -4); 

                        if (!empty($url2search_no_html)) 
                            $clean_url_html_sql_lookup = "or (value = '" . $url2search_no_html . "' and attribute_id IN ('".implode("','",$clean_url_html_attribute_ids)."'))";
                    }
 
                    $data = cw_query_first(
                        "SELECT item_id, item_type
                        FROM $tables[attributes_values]
                        WHERE attribute_id IN ('".implode("','",$clean_url_attrubute_ids)."') AND 
                        (value = '" . $url2search . "' $clean_url_html_sql_lookup) AND code = '$lang'"
                    );

                    if (empty($data)) {
                        array_unshift($clean_url_values, array_pop($clean_url_parts));
                    }
                } while (empty($data) && count($clean_url_parts));

                if (cw_clean_url_check_url_is_in_history($clean_url) && empty($data)) {
                    $redirect_url = cw_clean_url_get_url_by_history_url($clean_url);
                    $clean_urls[$urk_key] = $redirect_url;
                    $location = $current_location . '/' . implode('/', $clean_urls);
                    $location .= cw_clean_url_get_additional_query($url['query'], $params_query);
                    cw_header_location($location, true, false, 301);
                }

                $params = array(
                    'P' => array('target' => 'product', 'param' => 'product_id'),
                    'C' => array('target' => 'index', 'param' => 'cat'),
                    'M' => array('target' => 'manufacturers', 'param' => 'manufacturer_id'),
                    'AB' => array('target' => 'pages', 'param' => 'page_id'),
                    'Q' => array('target' => 'search', 'param' => 'mode'),
                );

                if ($data['item_type'] == 'O') {  // owner urls
                    $static_url = cw_query_first_cell("
                        SELECT value
                        FROM $tables[attributes_values]
                        WHERE item_id = '$data[item_id]' AND item_type = 'OS' AND value <> ''
                    ");

                    $parsed_static_url = parse_url($static_url);

                    if (!empty($parsed_static_url['query'])) {
                        parse_str($parsed_static_url['query'], $parsed_arr);

                        if (is_array($parsed_arr) && !empty($parsed_arr)) {

                            foreach ($parsed_arr as $_key => $_value) {
                                global $$_key;
                                $_GET[$_key] = $$_key = $_value;
                            }
                        }
                    }
                }
                elseif ($params[$data['item_type']]) {  // products, manufacturers, pages, categories urls
                    $is_switch_html_redirect = cw_call('cw_clean_url_html_is_redirect', array($data['item_type'], $clean_url)); 
                    if ($is_switch_html_redirect) {
                        $location = $current_location . '/' . $clean_url . ".html";
                        $location .= cw_clean_url_get_additional_query($url['query'], $params_query);
                        cw_header_location($location, true, false, 301);
                    }

                    global $target, ${$params[$data['item_type']]['param']};
                    $_GET['target'] = $target = $params[$data['item_type']]['target'];

                    if ($data['item_type'] == 'Q') {    // 'search' clean url
                        $data['item_id'] = $target;
                    }
                    $_GET[$params[$data['item_type']]['param']] = ${$params[$data['item_type']]['param']} = $data['item_id'];
                }
                elseif ($data['item_type'] == 'PA' || $data['item_type'] == 'AV') {   // attributes clean urls
                    $attribute_value_id = $data['item_id'];
                    // if $clean_url_values is empty, then one value (default value)
                    if (empty($clean_url_values)) {
                        $attribute_id = cw_query_first_cell(
                            "SELECT attribute_id
                            FROM $tables[attributes_default]
                            WHERE attribute_value_id = '$attribute_value_id'"
                        );

                        if (!empty($attribute_id)) {
                            global $att;
                            $_GET['att'][$attribute_id][] = $attribute_value_id;
                            $att[$attribute_id][] = $attribute_value_id;
                        }
                    }
                    else {
                        $attribute_type = cw_query_first_cell(
                            "SELECT type FROM $tables[attributes]
                            WHERE attribute_id = '$attribute_value_id' AND active = 1 AND pf_is_use = 1"
                        );
                        
                        global $att;

                        if (in_array($attribute_type, array("decimal", "integer"))) {
                            $attribute_field = cw_query_first_cell(
                                "SELECT field
                                FROM $tables[attributes]
                                WHERE attribute_id = '$attribute_value_id'"
                            );

                            $_GET['att'][$attribute_value_id]['min'] = $clean_url_values[0];
                            $_GET['att'][$attribute_value_id]['max'] = $clean_url_values[1];
                            $att[$attribute_value_id]['min'] = $clean_url_values[0];
                            $att[$attribute_value_id]['max'] = $clean_url_values[1];
                        }
                        elseif ($attribute_type == "manufacturer-selector") {
                                $manufacturer_item_id = cw_query_first_cell(
                                    "SELECT av.item_id
                                    FROM $tables[attributes_values] av
                                    LEFT JOIN $tables[attributes] a ON a.attribute_id = av.attribute_id
                                    WHERE av.value = '" . addslashes(implode('-', $clean_url_values)) . "'
                                        AND av.code = '$lang' AND av.item_type = 'M'
                                        AND a.field = 'clean_url' AND a.item_type = 'M'
                                        AND a.active = 1 AND a.addon = 'clean_urls'"
                                );
                                $_GET['att'][$attribute_value_id][] = $manufacturer_item_id;
                                $att[$attribute_value_id][] = $manufacturer_item_id;
                        }
                        else {
                                $_GET['att'][$attribute_value_id][] = implode('-', $clean_url_values);
                                $att[$attribute_value_id][] = implode('-', $clean_url_values);
                        }
                    }
                }
            }
        }
    }
    else {
        // get dynamic url and "find" clean url
        if (
            $REQUEST_METHOD == 'GET'
            && !defined('IS_AJAX')
            && isset($params_query[$target])
            && !empty($_GET[$params_query[$target][0]])
        ) {
            $item_id = intval($_GET[$params_query[$target][0]]);
            $attributes = cw_func_call('cw_attributes_get_attributes_by_field', array('field' => 'clean_url'));
            $type = $params_query[$target][1];

            $redirect_url = cw_query_first_cell(
                "SELECT value
                FROM $tables[attributes_values]
                WHERE item_id = '" . $item_id . "' AND item_type = '$type'
                    AND attribute_id = '$attributes[$type]' AND code = '$lang'"
            );

            if (!empty($redirect_url)) {
                $clean_url_html_attribute_ids = cw_call('cw_clean_url_html_attribute_ids', array()); 
                if (in_array($attributes[$type], $clean_url_html_attribute_ids)) {
                    $redirect_url .= ".html"; 
                }              

                $location = $current_location . '/' . $redirect_url;
                $location .= cw_clean_url_get_additional_query($url['query'], $params_query);
                cw_header_location($location, true, false, 301);
            }
        }
        elseif (    // translate real attributes urls to clean urls
            $REQUEST_METHOD == 'GET'
            && !defined('IS_AJAX')
            && !empty($_GET['att'])
        ) {
            parse_str($url['query'], $arr);
            $clean_url = cw_call('cw_clean_url_generate_filter_url', array(array('ns' => $url['path']), array('att' => $arr['att'])));

            if (!empty($clean_url)) {
                unset($arr['att']);
                $has_additional_query = FALSE;

                if (is_array($arr)) {

                    foreach ($arr as $_v) {

                        if (!empty($_v)) {
                            $has_additional_query = TRUE;
                            break;
                        }
                    }
                }
                $location = $clean_url . ($has_additional_query ? '?' . http_build_query($arr) : '');
                cw_header_location($location, true, false, 301);
            }
            else {
                cw_header_location($url['path'], true, false);
            }
        }
        elseif (  // try to find in owner urls
            $REQUEST_METHOD == 'GET'
            && !defined('IS_AJAX')
            && !empty($url['query'])
        ) {
            $redirect_url = cw_clean_url_get_redirect_url_in_owner_urls($url['query'], $lang);

            if (!empty($redirect_url)) {
                $location = $current_location . '/' . $redirect_url;
                cw_header_location($location, true, false, 301);
            }
        }
    }
}
