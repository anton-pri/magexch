<?php
cw_load('product.base','attributes', 'product-filter');

/*
product types
see init/constants.php
*/
/*
status
0 - disabled
1 - enabled
*/


/**
 * Delete product or all products
 * 
 * @param int $product_id
 * @param bool $update_categories - flag to update categories statistic
 * @param bool $delete_all - flag to delete all products, $product_id ignored
 * 
 * @return bool
 */
function cw_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $tables, $addons;

    cw_load('category', 'image', 'sections', 'attributes', 'tags');

    if ($delete_all === true) {
        $tables_to_clear = array('products', 'products_stats', 'products_prices', 'products_warehouses_amount', 'featured_products', 'products', 'attributes_values', 'products_categories', 'products_votes', 'products_reviews', 'products_lng', 'download_keys', 'discount_coupons', 'products_bookmarks', 'products_memberships');

        foreach($tables_to_clear as $table)
            db_query("delete from ".$tables[$table]);

        cw_delete_sections($product_id);

        cw_image_delete('products_images_thumb');
        cw_image_delete('products_images_det');
        cw_image_delete('products_detailed_images');


/*
# kornev, TOFIX
* TODO: move to addon hook
        # magnifier addon
        if (cw_query_first_cell("SELECT addon_name FROM $tables[addons] WHERE addon_name='magnifier'")) {
            if (!isset($tables['magnifier_images']))
                include_once $app_main_dir.'/addons/magnifier/config.php';

            db_query("DELETE FROM $tables[magnifier_images]");
            $dir_z = cw_image_dir("Z");
            if (is_dir($dir_z) && file_exists($dir_z))
                cw_rm_dir($dir_z);
        }
*/
        if ($update_categories) {
            $res = db_query("SELECT category_id FROM $tables[categories]");
            cw_recalc_subcat_count($res);
        }


        db_query("DELETE FROM $tables[products_flat]");

        return true;
    }

    $product_categories = cw_query_column("select $tables[categories_parents].parent_id from $tables[categories_parents], $tables[products_categories] where $tables[categories_parents].category_id = $tables[products_categories].category_id and $tables[products_categories].product_id='$product_id' group by $tables[categories_parents].parent_id");
         
    cw_delete_section_product($product_id);
    db_query("DELETE FROM $tables[products_stats] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[products_prices] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[products_warehouses_amount] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[featured_products] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[products_shipping] WHERE product_id='$product_id'");

    cw_call('cw_attributes_cleanup', array($product_id,'P'));

    db_query("DELETE FROM $tables[products_memberships] WHERE product_id='$product_id'");
    cw_image_delete($product_id, 'products_images_thumb');
    cw_image_delete($product_id, 'products_images_det');

    Product\delete($product_id); // associate your event handlers with on_product_delete event triggered in this method

    # Product options addon
/*
# kornev, TOFIX
* TODO: move to addon hook
    # magnifier addon
    if (cw_query_first_cell("SELECT addon_name FROM $tables[addons] WHERE addon_name='magnifier'")) {
        if (!isset($tables['magnifier_images'])) {
            include_once $app_main_dir."/addons/magnifier/config.php";
        }

        db_query("DELETE FROM $tables[magnifier_images] WHERE id = '$product_id'");
        $dir_z = cw_image_dir("Z").DIRECTORY_SEPARATOR.$product_id;
        if (is_dir($dir_z) && file_exists($dir_z))
            cw_rm_dir($dir_z);
    }
*/

    db_query("DELETE FROM $tables[products_votes] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[products_reviews] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[products_lng] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[download_keys] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[discount_coupons] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[products_bookmarks] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[products_system_info] WHERE product_id='$product_id'");

    #
    # Update product count for categories
    #
    if ($update_categories && !empty($product_categories))
        cw_recalc_subcat_count($product_categories);

    db_query("DELETE FROM $tables[products_flat] WHERE product_id = '$product_id'");
    cw_tags_clear_product_tags($product_id);

    return true;
}

# kornev, params
# 'data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => $info_type,
# info type values
# 1024 - product filter
function cw_product_search($params, $return = null) {
    extract($params);

    global $tables, $addons, $current_language, $config, $app_config_file;
    global $cart;
    global $smarty;
    global $target;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $from_tbls[] = 'products';

# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    if ($data['where']) $where[] = $data['where'];

# kornev, all of the inner joins are replaced by left join && not null
# it works fastly because mylsq do not optimize the tables order is query and we are able to play with it.

    $memberships = array(0);
    if ($user_account['membership_id']>0) $memberships[] = intval($user_account['membership_id']);

    $query_joins['products_flat'] = array(
        'on' => "$tables[products_flat].product_id = $tables[products].product_id",
        'only_select' => 1,
    );
    $fields[] = "$tables[products_flat].*";
    $fields[] = "$tables[products].*";

    if (!is_array($data['warehouse_customer_id']))
        $data['warehouse_customer_id'] = array($data['warehouse_customer_id'] => 1);

    $query_joins['products_warehouses_amount'] = array(
        'parent' => 'products',
        'on' => "$tables[products_warehouses_amount].product_id = $tables[products].product_id and $tables[products_warehouses_amount].warehouse_customer_id = 0 and $tables[products_warehouses_amount].variant_id=0",
    );

   $fields[] = "$tables[products_warehouses_amount].avail";

    if ($current_area == 'A') {
        $fields[] = "$tables[products_prices].variant_id";
        $fields[] = "$tables[products_prices].price";

# kornev, TOFIX
        if ($addons['product_options'])
            $fields[] = "if($tables[product_variants].product_id IS NULL, '', 'Y') as is_variants";

        $query_joins['products_prices'] = array(
            'on' => "$tables[products_prices].product_id = $tables[products].product_id",
            'is_inner' => 1,
        );

    }
    else {
        $fields[] = "min($tables[products_prices].price) as price";
        $fields[] = "$tables[products_prices].variant_id";
        $fields[] = "$tables[products_prices].list_price";
//        $fields[] = "min($tables[products_prices].list_price) as list_price";

        $query_joins['products_prices'] = array(
            'parent' => 'products',
            'on' => "$tables[products_prices].product_id = $tables[products].product_id and $tables[products_prices].quantity=1 and $tables[products_prices].membership_id in (".join(',',$memberships).")",
            'is_inner' => 1,
        );

        if ($config['Appearance']['show_views_on_product_page'] == 'Y') {
            $fields[] = "$tables[products_stats].views_stats";
            $query_joins['products_stats'] = array(
                'on' => "$tables[products_stats].product_id = $tables[products].product_id",
                'only_select' => 1,
            );
        }
    }

    if ($config['Appearance']['categories_in_products'] == '1') {
        $query_joins['products_categories'] = array(
            'on' => "$tables[products_categories].product_id = $tables[products].product_id",
                'pos' => '0',
                'is_inner' => 1,
        );
    }

# kornev, we should add filter by attributes names
    if ($data['attribute_names']) {
        $att_ids = cw_call('cw_attributes_get_attribute_by_field', array('field' => array_keys($data['attribute_names'])));
        if ($att_ids)
        foreach($att_ids as $k=>$v)
            $data['attributes'][$v] = $data['attribute_names'][$k];
    }

# kornev, filter by attributes
    if ($data['attributes']) {
        if ($data['attributes'][PRICE_ATTRIBUTE_ID]) {
            $data['price_min'] = $data['attributes'][PRICE_ATTRIBUTE_ID]['min'];
            $data['price_max'] = $data['attributes'][PRICE_ATTRIBUTE_ID]['max'];
        }
        if ($data['attributes']['substring']) {
            $data['substring'] = $data['attributes']['substring'];
        }
        ksort($data['attributes']);
        foreach($data['attributes'] as $k=>$v) {
            
            $a = cw_call('cw_attributes_filter',array(array('attribute_id'=>$k), true));
            // If attr is number and presented in filter as predefined ranges, 
            // then range attribute_value_id is passed as criteria, not real value or real range
            if (in_array($a['type'], array('decimal','integer'),true) 
                && in_array($a['pf_display_type'],array('P','W','E','G'))
                && !isset($v['min']) ) {
                    
                    $result = cw_product_filter_get_price_range_values($v[0]);
                    if($result){
                      $v['min'] = $result[0];
                      $v['max'] = $result[1];
                    }
                    if ($k == PRICE_ATTRIBUTE_ID) {
                        $data['price_min'] = $v['min'];
                        $data['price_max'] = $v['max'];
                        $data['attributes']['price'] = $v;
                        continue;
                    }
            }
            if (in_array($k, array('price', 'substring', PRICE_ATTRIBUTE_ID))) continue;
            if (!is_numeric($k)) continue; // Only real attributes must e converted into JOIN sql statement
            if (!is_array($v)) $data['attributes'][$k] = $v = array($v);

            $query_joins['atv_'.$k] = array(
                'tblname' => 'attributes_values',
                'on' => "$tables[products].product_id=atv_$k.item_id and atv_$k.attribute_id = '$k' and atv_$k.code in ('$current_language', '') and ".(isset($v['min'])?"atv_$k.value >= ".floatval($v['min'])." and atv_$k.value <= ".floatval($v['max']):"atv_$k.value in ('".implode("', '", $v)."')"),
                'is_inner' => 1,
            );
        }
    }


    if (!empty($data['by_pf_s_attr']) && !empty($data['substring'])) {
        $attr_pf_s_where = array();
        foreach ($data['by_pf_s_attr'] as $attr_id => $digit_flag) {

            $is_def_values = cw_query_first_cell("select count(*) from $tables[attributes_default] where attribute_id='$attr_id'");
            $attr_type = cw_query_first_cell("select type from $tables[attributes] where attribute_id='$attr_id'");

            if ($is_def_values && !in_array($attr_type, array('textarea', 'text'))) {
                if ($data['including'] == 'all' || $data['including'] == 'any') {
                    $search_words = array_filter(explode(" ", $data['substring']));
                    $search_words_arr = array();
                    foreach ($search_words as $word) {
                        $word = addslashes($word); 
                        $search_words_arr[] = "atd_pf_s_$attr_id.value like '%$word%'";
                    }
                }

                if ($data['including'] == 'all') {
                    $value_eq_sql = " (".implode(" and ", $search_words_arr).") ";
                } elseif ($data['including'] == 'any')  {
                    $value_eq_sql = " (".implode(" or ", $search_words_arr).") ";
                } else {
                    $value_eq_sql = "atd_pf_s_$attr_id.value like '%".addslashes($data['substring'])."%'";
                }
                $attr_value_ids = cw_query_column("select attribute_value_id from $tables[attributes_default] atd_pf_s_$attr_id where atd_pf_s_$attr_id.attribute_id='$attr_id' and $value_eq_sql");
                if (!empty($attr_value_ids)) {
                    $query_joins['atv_pf_s_'.$attr_id] = array(
                        'tblname' => 'attributes_values',
                        'on' => "$tables[products].product_id=atv_pf_s_$attr_id.item_id and atv_pf_s_$attr_id.item_type='P' and atv_pf_s_$attr_id.attribute_id = '$attr_id' and atv_pf_s_$attr_id.code in ('$current_language', '') and cast(atv_pf_s_$attr_id.value as SIGNED) in ('".implode("','",$attr_value_ids)."')",
                    );
                    $attr_pf_s_where[] = "atv_pf_s_$attr_id.item_id is not null";
                }
            } else {

                if ($data['including'] == 'all' || $data['including'] == 'any') {
                    $search_words = array_filter(explode(" ", $data['substring']));
                    $search_words_arr = array();
                    foreach ($search_words as $word) {
                        $word = addslashes($word);   
                        $search_words_arr[] = "atv_pf_s_$attr_id.value like '%$word%'";
                    }
                }
                if ($data['including'] == 'all') {
                    $value_eq_sql = " (".implode(" and ", $search_words_arr).") ";
                } elseif ($data['including'] == 'any')  {
                    $value_eq_sql = " (".implode(" or ", $search_words_arr).") ";
                } else {
                    $value_eq_sql = "atv_pf_s_$attr_id.value like '%".addslashes($data['substring'])."%'";
                }

                $query_joins['atv_pf_s_'.$attr_id] = array(
                    'tblname' => 'attributes_values',
                    'on' => "$tables[products].product_id=atv_pf_s_$attr_id.item_id and atv_pf_s_$attr_id.item_type='P' and atv_pf_s_$attr_id.attribute_id = '$attr_id' and atv_pf_s_$attr_id.code in ('$current_language', '') and $value_eq_sql",
                );
                $attr_pf_s_where[] = "atv_pf_s_$attr_id.item_id is not null";
            }
            $return['substring_search_alternative'] = true;
        }
        if (!empty($attr_pf_s_where)) 
            $where[] = "(".implode(" or ", $attr_pf_s_where).")"; 
    }

    if (!empty($data['by_pf_attr_numeric'])) {
        $cast_types = array('decimal'=>'DECIMAL', 'integer'=>'SIGNED', 'yes_no'=>'SIGNED');
        foreach ($data['by_pf_attr_numeric'] as $attr_id => $attr_limits) {
            if (empty($attr_limits['min']) && empty($attr_limits['max'])) continue;

            $attr_type = cw_query_first_cell("select type from $tables[attributes] where attribute_id='$attr_id'");
            $_cast_type = $cast_types[$attr_type];
            if (empty($_cast_type)) continue;

            $ps_numeric_val_limits = array();
            $_min_limit = min($attr_limits['min'], $attr_limits['max']);
            $_max_limit = max($attr_limits['min'], $attr_limits['max']);

            $ps_num_table_alias = "ps_num_".$attr_id;

            if (!empty($_min_limit))
                $ps_numeric_val_limits[] = "cast(".$ps_num_table_alias.".value as $_cast_type) >= '$_min_limit'";

            if (!empty($_max_limit))
                $ps_numeric_val_limits[] = "cast(".$ps_num_table_alias.".value as $_cast_type) <= '$_max_limit'";

            if (!empty($ps_numeric_val_limits)) {
                $query_joins[$ps_num_table_alias] = array(
                    'tblname' => 'attributes_values',
                    'on' => "$tables[products].product_id=$ps_num_table_alias.item_id and $ps_num_table_alias.item_type='P' and $ps_num_table_alias.attribute_id='$attr_id' and ".implode(" and ", $ps_numeric_val_limits),
                    'is_inner' => 1
                );
            }
        }
    }

    if (isset($data['by_pf_attr_multi'])) {
        foreach ($data['by_pf_attr_multi'] as $attr_id => $attr_value_ids) {
            if (empty($attr_value_ids)) continue;
            $ps_multi_table_alias = 'atv_pf_multi_'.$attr_id;
            $query_joins[$ps_multi_table_alias] = array(
                'tblname' => 'attributes_values',
                'on' => "$tables[products].product_id=$ps_multi_table_alias.item_id and $ps_multi_table_alias.item_type='P' and $ps_multi_table_alias.attribute_id = '$attr_id' and $ps_multi_table_alias.code in ('$current_language', '') and (cast($ps_multi_table_alias.value as SIGNED) in ('".implode("','",$attr_value_ids)."'))",
                'is_inner' => 1
            );
        }
    }

    if (!$data['substring_exact']) {
        $data['substring'] = trim($data['substring']);
        $data['substring_prepared'] = '%'.$data['substring'].'%';
    }
    else
        $data['substring_prepared'] = $data['substring'];

    $search_by_variants = false;

    if (($data['search_sections']['tab_basic_search'] || $data['flat_search'] || $data['attributes']['substring']) && $data['substring']) {
        $condition = array();
        $search_string_fields = array();
        if (empty($data['by_title']) && empty($data['by_shortdescr']) && empty($data['by_fulldescr']) && empty($data['by_sku']) && empty($return['substring_search_alternative'])) {
            $search_data['products'][$use_search_conditions]['by_title'] = $data['by_title'] = 1; // TOFIX: both $search_data and $use_search_conditions is undefinde
            $flag_save = true;
        }

        if ($data['by_title'])
            $search_string_fields[] = "product";
        if ($data['by_manufacturer'])
            $search_string_fields[] = 'manufacturer_code';
        if ($data['by_productcode'])
            $search_string_fields[] = 'productcode';
        if ($data['by_ean'])
            $search_string_fields[] = 'eancode';
        if ($data['by_shortdescr'])
            $search_string_fields[] = "descr";
        if ($data['by_fulldescr'])
            $search_string_fields[] = "fulldescr";

        if ($data['by_ean'] && !empty($addons['product_options'])) {
            $search_by_variants = true;
            $condition[] = "search_variants.eancode LIKE '".addslashes($data['substring_prepared'])."'";
        }
        if ($data['by_manufacturer'] && !empty($addons['product_options'])) {
            $search_by_variants = true;
            $condition[] = "search_variants.mpn LIKE '".addslashes($data['substring_prepared'])."'";
        }
        
        $search_words = array();
        if ($config['search']['allow_search_by_words'] == 'Y' && in_array($data['including'], array("all", "any"))) {
            $tmp = stripslashes(trim($data['substring']));
            if (preg_match_all('/"([^"]+)"/', $tmp, $match)) {
                $search_words = $match[1];
                $tmp = str_replace($match[0], '', $tmp);
            }
            $tmp = explode(' ', $tmp);
            $tmp = cw_array_map("trim", $tmp);
            $search_words = array_merge($search_words, $tmp);
            unset($tmp);

            # Check word length limit
            if ($search_word_length_limit > 0) {
                $search_words = preg_grep("/^..+$/", $search_words);
            }

            $stopwords = cw_get_stopwords();
            if (!empty($stopwords) && is_array($stopwords)) {
                $tmp = preg_grep("/^(".implode("|", $stopwords).")$/i", $search_words);
                if (!empty($tmp) && is_array($tmp)) {
                    $search_words = array_diff($search_words, $tmp);
                    $search_words = array_values($search_words);
                }
                unset($tmp);
            }

            # Check word count limit
            if ($search_word_limit > 0 && count($search_words) > $search_word_limit) {
                $search_words = array_splice($search_words, $search_word_limit-1);
            }
        }
        $search_words = array_filter($search_words);
        $search_words = cw_addslashes($search_words);

        foreach ($search_string_fields as $ssf) {
            if ($config['search']['allow_search_by_words'] == 'Y' && !empty($search_words) && in_array($data['including'], array("all", "any"))) {
                if ($data['including'] == 'all') {
                    $tmp = array();
                    foreach ($search_words as $sw) {
                        if (($current_area == 'C' || $current_area == 'B') && !defined('ONLY_ONE_LANGUAGE')) {
                            if (in_array($ssf, array('productcode', 'manufacturer_code', 'eancode')))
                                $tmp[] = "$tables[products].$ssf LIKE '%".$sw."%'";
                            else
                                $tmp[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].$ssf, $tables[products].$ssf) LIKE '%".$sw."%'";


                        } else {
                            $tmp[] = "$tables[products].$ssf LIKE '%".$sw."%'";
                        }
                    }
                    if (!empty($tmp))
                        $condition[] = "(".implode(" AND ", $tmp).")";
                    unset($tmp);


                } else {
                    if (($current_area == 'C' || $current_area == 'B') && !defined('ONLY_ONE_LANGUAGE')) {
                        if (in_array($ssf, array('productcode', 'manufacturer_code', 'eancode')))
                            $condition[] = "$tables[products].$ssf REGEXP '".implode("|", $search_words)."'";
                        else
                            $condition[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].$ssf, $tables[products].$ssf) REGEXP '".implode("|", $search_words)."'";
                    } else {
                        $condition[] = "$tables[products].$ssf REGEXP '".implode("|", $search_words)."'";
                    }
                }

            } elseif  (($current_area == 'C' || $current_area == 'B') && !defined('ONLY_ONE_LANGUAGE')) {
                if (in_array($ssf, array('productcode', 'manufacturer_code', 'eancode')))
                    $condition[] = "$tables[products].$ssf LIKE '".$data['substring_prepared']."'";
                else
                    $condition[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].$ssf, $tables[products].$ssf) LIKE '".$data['substring_prepared']."'";

            } else {
                $condition[] = "$tables[products].$ssf LIKE '".$data['substring_prepared']."'";
            }
        }

        if ($data['by_sku']) {
            $search_by_variants = true;
# kornev, TOFIX
            $condition[] = (empty($addons['product_options']) ? "($tables[products].productcode)" : "(IFNULL(search_variants.productcode, $tables[products].productcode)")." LIKE '".$data['substring_prepared']."' OR $tables[products].eancode like '".$data['substring_prepared']."')";
        }
        
        if ($data['by_tags'] || $data['by_shortdescr'] || $data['by_fulldescr']) {
            if (!empty($search_words) && in_array($data['including'], array("all", "any"))) {
                $tags_condition = "name REGEXP '".implode("|", $search_words)."'";
            } else {
                $tags_condition = "name LIKE '".$data['substring_prepared']."'";
            }
            $tag_ids = cw_query_column("SELECT tag_id FROM $tables[tags] WHERE $tags_condition");
            
            $query_joins['tags_substr'] = array(
                'tblname' => 'tags_products',
                'on' => "tags_substr.product_id = $tables[products].product_id",
                'only_select' => 0,
            );

            $condition[] = "tags_substr.tag_id IN ('".join("','",$tag_ids)."')";
            
            unset($tag_ids, $tags_condition);
        }

        if (!empty($condition))
            $where[] = "(".implode(" OR ", $condition).")";
        unset($condition);

    }

    if ($data['search_sections']['tab_add_search'] || $data['flat_search'] || $data['product_types']) {
    
        if (!$data['status']) $data['status'] = cw_core_get_required_status($current_area);
        if ($data['status']) {
            $where[] = "$tables[products].status in (".implode(", ", $data['status']).")";
        }
    
        if ($config['Appearance']['categories_in_products'] == '1') {
            if ($data['category_id']) {
                $data['categories'] = array($data['category_id'] => 1);
                $data['categories_orig'] = array($data['category_id']);
            }

            if ($data['category_ids']) {
                $_categories = array();
                $_categories_orig = array();
                foreach ($data['category_ids'] as $c_id) {
                    $_categories[$c_id] = 1;
                    $_categories_orig[] = $c_id;   
                } 
                $data['categories'] = $_categories;
                $data['categories_orig'] = $_categories_orig;  
            }  

            if ($data['categories'] && is_array($data['categories'])) {
                if ($data['search_in_subcategories']) {
                    $categories_to_search = cw_category_get_subcategory_ids($data['categories_orig']);
                    if (count($categories_to_search))
                       $where[] = "$tables[products_categories].category_id IN (".implode(",", $categories_to_search).")";
                }
                else
                    $where[] = "$tables[products_categories].category_id IN (".implode(",", $data['categories_orig']).")";

                $condition = array();
                if ($data['category_main'])
                    $condition[] = "$tables[products_categories].main = 1";
                if ($data['category_extra'])
                    $condition[] = "$tables[products_categories].main = 0";
                if (count($condition))
                    $where[] = "(".implode(" OR ", $condition).")";
            }
        }

        if ($data['productcode']) {
            $search_by_variants = true;
# kornev, TOFIX
            $productcode_cond_string = empty($addons['product_options']) ? "$tables[products].productcode" : "IFNULL(search_variants.productcode, $tables[products].productcode)";
            $where[] = "($productcode_cond_string LIKE '%".addslashes($data['productcode'])."%')";
        }

        if ($data['manufacturer_code'])
            $where[] = "($tables[products].manufacturer_code like '%".$data['manufacturer_code']."%')";

        if ($data['eancode']) {
            $search_by_variants = true;
# kornev, TOFIX
            $productcode_cond_string = empty($addons['product_options']) ? "$tables[products].eancode" : "IFNULL(search_variants.eancode, $tables[products].eancode)";
            $where[] = "($productcode_cond_string LIKE '%".addslashes($data['eancode'])."%')";
        }

        if ($data['product_id'])
            $where[] = "$tables[products].product_id ".(is_array($data['product_id']) ? " IN ('".implode("','", $data['product_id'])."')": "= '".$data['product_id']."'");

        if (!empty($data['serial_number'])) {
            $warehouse_condition = (AREA_TYPE == 'P'?"and $tables[serial_numbers].warehouse_customer_id='$user_account[warehouse_customer_id]'":"");
            $where[] = "$tables[serial_numbers].sn LIKE '%".$data['serial_number']."%' $warehouse_condition";
            $query_joins['serial_numbers'] = array(
                "on" => "$tables[serial_numbers].product_id = $tables[products].product_id",
                'is_inner' => 1,
            );
        }

        if ($data['avail_types']) {
            if ($data['avail_types'][1])
                $where[] = "$tables[products_warehouses_amount].avail > 0";
            if ($data['avail_types'][2])
                $where[] = "$tables[products_warehouses_amount].avail_ordered > 0";
            if ($data['avail_types'][3])
                $where[] = "$tables[products_warehouses_amount].avail_sold > 0";
            if ($data['avail_types'][4])
                $where[] = "$tables[products_warehouses_amount].avail_reserved > 0";
            if ($data['avail_types'][5])
                $where[] = "$tables[products_warehouses_amount].avail < 0";
            if ($data['avail_types'][6])
                $where[] = "$tables[products_warehouses_amount].avail = 0";               
        }

        if ($data['product_types'])
            $where[] = "$tables[products].product_type in ('".implode("', '", array_keys($data['product_types']))."')";
    }

    if (($current_area == "C" || $current_area == "B") && $config['General']['disable_outofstock_products'] == "Y")
        $where[] = "$tables[products_warehouses_amount].avail > 0";

    if ($data['search_sections']['tab_prices'] || $data['flat_search']) {
        $query_joins['products_prices'] = array(
            'parent' => 'products',
            'on' => "$tables[products_prices].product_id = $tables[products].product_id and $tables[products_prices].quantity=1 and $tables[products_prices].membership_id in (".join(',',$memberships).")",
            'is_inner' => 1,
        );

        if ($data['price_min'])
            $where['price_min'] = "$tables[products_prices].price >= '$data[price_min]'";
        if ($data['price_max'])
            $where['price_max'] = "$tables[products_prices].price <= '$data[price_max]'";

        if ($data['weight_min']) 
            $where['weight_min'] = "$tables[products].weight >= '$data[weight_min]'";
        if ($data['weight_max'])
            $where['weight_max'] = "$tables[products].weight <= '$data[weight_max]'";
  

/*
        if ($data['list_price_min'])
            $where[] = "$tables[products_prices].list_price >= '$data[list_price_min]'";
        if ($data['list_price_min'])
            $where[] = "$tables[products_prices].list_price <= '$data[list_price_max]'";
*/
        if ($data['list_price_min'])
            $where[] = "$tables[products].list_price >= '$data[list_price_min]'";
        if ($data['list_price_min'])
            $where[] = "$tables[products].list_price <= '$data[list_price_max]'";
    }

    if ($data['search_sections']['tab_additional_options'] || $data['flat_search']) {
        if ($data['flag_free_ship'])
            $where[] = "$tables[products].free_shipping = '".$data['flag_free_ship']."'";

        if ($data['flag_ship_freight'])
            $where[] = "$tables[products].shipping_freight ".($data['flag_ship_freight'] == 'Y'?'>0':'=0');

        if ($data['flag_global_disc'])
            $where[] = "$tables[products].discount_avail = '".$data['flag_global_disc']."'";

        if ($data['flag_free_tax'])
            $where[] = "$tables[products].free_tax = '".$data['flag_free_tax']."'";

        if ($data['flag_min_amount']) {
            if ($data['flag_min_amount'] == "Y")
                $where[] = "$tables[products].min_amount <= 1";
            else
                $where[] = "$tables[products].min_amount = 1";
        }

        if ($data['flag_low_avail_limit']) {
            if ($data['flag_low_avail_limit'] == 'Y')
                $where[] = "$tables[products].low_avail_limit <= 1";
            else
                $where[] = "$tables[products].low_avail_limit > 1";
        }
    }

    if ($data['search_sections']['tab_additional_options'] || $data['flat_search']) {

        if ($data['has_image']) {
            if ($data['has_image'] == 'U') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id AND $tables[products_images_det].image_path NOT like 'http://%'",
                    'is_inner' => 1,
                );
            } 
            elseif ($data['has_image'] == 'UL') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id",
                    'is_inner' => 1,
                );
            }             
            elseif ($data['has_image'] == 'L') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id AND $tables[products_images_det].image_path like 'http://%'",
                    'is_inner' => 1,
                );
            } 
            elseif ($data['has_image'] == 'N') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id",
                    'is_inner' => 0,
                );
                $where['has_image'] = "$tables[products_images_det].image_id IS NULL";
            } 
            elseif ($data['has_image'] == 'NL') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id",
                    'is_inner' => 0,
                );
                $where['has_image'] = "$tables[products_images_det].image_id IS NULL OR $tables[products_images_det].image_path like 'http://%'";
            }
        }

        if ($data['has_image']) {
            if ($data['has_image'] == 'U') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id AND $tables[products_images_det].image_path NOT like 'http://%'",
                    'is_inner' => 1,
                );
            } 
            elseif ($data['has_image'] == 'UL') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id",
                    'is_inner' => 1,
                );
            }             
            elseif ($data['has_image'] == 'L') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id AND $tables[products_images_det].image_path like 'http://%'",
                    'is_inner' => 1,
                );
            } 
            elseif ($data['has_image'] == 'N') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id",
                    'is_inner' => 0,
                );
                $where['has_image'] = "$tables[products_images_det].image_id IS NULL";
            } 
            elseif ($data['has_image'] == 'NL') {
                $query_joins['products_images_det'] = array(
                    'on' => "$tables[products_images_det].id = $tables[products].product_id",
                    'is_inner' => 0,
                );
                $where['has_image'] = "$tables[products_images_det].image_id IS NULL OR $tables[products_images_det].image_path like 'http://%'";
            }
        }

        if ($data['blank_descr'])
            $where[] = "$tables[products].descr = ''";

        if ($data['code']) {
            $search_by_variants = true;
# kornev, TOFIX
            $productcode_cond_string = empty($addons['product_options']) ? "$tables[products].eancode" : "IFNULL(search_variants.eancode, $tables[products].eancode)";
            $where[] = "($productcode_cond_string != '')";
        }
        if ($data['without_code']) {
            $search_by_variants = true;
# kornev, TOFIX
            $where[] = !$addons['product_options'] ? "($tables[products].productcode = '' or $tables[products].eancode = '')" : "(IFNULL(search_variants.productcode, $tables[products].productcode) = '' OR IFNULL(search_variants.eancode, $tables[products].eancode) = '')";
        }

        if ($data['creation_date_start'] || 
            $data['creation_date_end'] ||
            $data['modify_date_start'] ||
            $data['modify_date_end'] ||
            $data['created_by'] ||
            $data['supplier']
            ) {
            
            $query_joins['products_system_info'] = array(
                'parent' => 'products',
                'on' => "$tables[products_system_info].product_id = $tables[products].product_id",
                'is_inner' => 1,
            );
            
            if ($data['creation_date_start'])
                $where[] = "$tables[products_system_info].creation_date >= '$data[creation_date_start]'";
            if ($data['creation_date_end'])
                $where[] = "$tables[products_system_info].creation_date <= '$data[creation_date_end]'";

            if ($data['modify_date_start'])
                $where[] = "$tables[products_system_info].modification_date >= '$data[modify_date_start]'";
            if ($data['modify_date_end'])
                $where[] = "$tables[products_system_info].modification_date <= '$data[modify_date_end]'";

            if ($data['created_by'])
                $where[] = "$tables[products_system_info].creation_customer_id = '$data[created_by]'";

            if ($data['supplier'])
                $where[] = "$tables[products_system_info].supplier_customer_id = '$data[supplier]'";
        }
        
        if ($data['sold_date_start'] || $data['sold_date_end']) {
            $query_joins['docs_items'] = array(
                'on' => "$tables[docs_items].product_id = $tables[products].product_id",
                'is_inner' => 1,
            );
               $query_joins['docs'] = array(
                'on' => "$tables[docs].doc_id = $tables[docs_items].doc_id",
                'is_inner' => 1,
            );

            if ($data['sold_date_start'])
                $where[] = "$tables[docs].date >= '$data[sold_date_start]'";
            if ($data['sold_date_end'])
                $where[] = "$tables[docs].date <= '$data[sold_date_end]'";
        }
    }

    if ($data['tag']) {
        $query_joins['tags_products'] = array(
            'on' => "$tables[tags_products].product_id = $tables[products].product_id",
            'only_select' => 0,
        );
        $query_joins['tags'] = array(
            'on' => "$tables[tags].tag_id = $tables[tags_products].tag_id",
            'only_select' => 0,
        );
        $where[] = "$tables[tags].name = '$data[tag]'";
    }

     if (!defined('ONLY_ONE_LANGUAGE')) {
        $fields[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].product, $tables[products].product) as product";
        $fields[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].descr, $tables[products].descr) as descr";
        $fields[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].fulldescr, $tables[products].fulldescr) as fulldescr";

        $query_joins['products_lng'] = array(
            'on' => "$tables[products_lng].product_id = $tables[products].product_id AND $tables[products_lng].code = '$current_language'",
            'only_select' => 1,
        );
        if ($data['by_title'] || $data['by_shortdescr'] || $data['by_fulldescr'])
            $query_joins['products_lng']['only_select'] = 0;
    }

    if (in_array($current_area, array('C', 'R', 'G'))) {
        if ($config['Appearance']['categories_in_products'] == '1') {
            $query_joins['categories_memberships'] = array(
                'on' => "$tables[categories_memberships].category_id = $tables[products_categories].category_id",
                'parent' => 'products_categories',
                'is_inner' => 1,
            );
        }
        $query_joins['products_memberships'] = array(
            'parent' => 'products',
            'on' => "$tables[products_memberships].product_id = $tables[products].product_id",
            'is_inner' => 1,
        );
    }
    if ($current_area == 'C') {
        if ($config['Appearance']['categories_in_products'] == '1') {
            $where[] = "$tables[categories_memberships].membership_id IN (".join(',',$memberships).")";
            $where[] = "$tables[categories].status = 1";
            $query_joins['categories_memberships']['only_select'] = 0;
            $query_joins['categories'] = array(
                'parent' => 'products_categories',
                'on' => "$tables[categories].category_id = $tables[products_categories].category_id",
                'is_inner' => 1
            );
        }

        $where[] = "$tables[products_memberships].membership_id IN (".join(',',$memberships).")";
        $query_joins['products_memberships']['only_select'] = 0;
    }
    elseif ($config['Appearance']['categories_in_products'] == '1' && $current_area == 'B') {
        $where[] = "$tables[categories].status = 1";
        $query_joins['categories'] = array(
            'parent' => 'products_categories',
            'on' => "$tables[categories].category_id = $tables[products_categories].category_id",
            'is_inner' => 1
        );
    }

    if ($config['Appearance']['categories_in_products'] == '1' && $data['get_category'] == 'Y') {
        $query_joins['categories'] = array(
            'parent' => 'products_categories',
            'on' => "$tables[categories].category_id = $tables[products_categories].category_id",
        );
        $query_joins['categories_lng'] = array(
            "on" => "$tables[categories_lng].category_id = $tables[categories].category_id and $tables[categories_lng].code='$current_language'",
            "parent" => "categories"
        );
        $fields[] = "IF($tables[categories_lng].category_id IS NOT NULL AND $tables[categories_lng].category != '', $tables[categories_lng].category, $tables[categories].category) as category";
    }

    if (isset($data['avail_min'])) {
        $where[] = "$tables[products_warehouses_amount].avail >= '".$data['avail_min']."'";
    }
   if (isset($data['avail_max'])) {
        $where[] = "$tables[products_warehouses_amount].avail <= '".$data['avail_max']."'";
    }
# kornev, TOFIX
    if($addons['product_options']) {
        if ($search_by_variants) {
            $query_joins['search_variants'] = array(
                'tblname' => 'product_variants',
                'on' => "search_variants.product_id = $tables[products].product_id",
                'only_select' => 0,
            );
        }
        if ($current_area == 'A')
# kornev, TOFIX
            $query_joins['product_variants'] = array(
            'on' => "$tables[product_variants].product_id = $tables[products].product_id AND $tables[product_variants].variant_id = $tables[products_prices].variant_id",
            'parent' => 'products_prices',
            'only_select' => 1,
            );
        else
            $query_joins['product_variants'] = array(
            'on' => "$tables[product_variants].product_id = $tables[products].product_id AND $tables[product_variants].variant_id = $tables[products_prices].variant_id",
            'parent' => 'products_prices',
            'only_select' => 1,
            );

        foreach (array('weight', 'productcode') as $property)
            $fields[] = "ifnull($tables[product_variants].$property, $tables[products].$property) as ".$property;
    }

    global $allowed_products_sort_fields;

    if (!isset($allowed_products_sort_fields))
        $allowed_products_sort_fields = array();

    if (!in_array($data['sort_field'], array('rand', 'productcode', 'title', 'orderby', 'quantity', 'price', 'product_id')) && !in_array($data['sort_field'], $allowed_products_sort_fields)) 
        $data['sort_field'] = $config['Appearance']['products_order'];

    if (!in_array($data['sort_direction'], array(0,1)))
        $data['sort_direction'] = 0;

    if (!empty($data['sort_field'])) {
        $direction = $data['sort_direction'] ? 'DESC' : 'ASC';

        if (
            $config['Appearance']['display_productcode_in_list'] != "Y"
            && ($current_area == 'C' || $current_area == 'B')
            && $data['sort_field'] == 'productcode'
        ) {
            $data['sort_field'] = 'orderby';
        }

        switch ($data['sort_field']) {
            case 'rand':
                $sort_string = 'RAND()';
                break;
            case "productcode":
                $sort_string = "$tables[products].productcode $direction";
                break;
            case "title":
                $sort_string = "$tables[products].product $direction";
                break;
            case "orderby":
                if ($config['Appearance']['categories_in_products'] == '1') {
                    $sort_string = "$tables[products_categories].orderby $direction";
                } else {
                    $sort_string = "$tables[products].productcode $direction";
                }
                break;
            case "quantity":
                $sort_string = "$tables[products_warehouses_amount].avail $direction";
                break;
            case "price":
                $sort_string = "price $direction";
                break;
            case "product_id":
                $sort_string = "$tables[products].product_id $direction";
                break;
            default:
                $sort_string = $data['sort_field'] . ' ' . $direction;               // special case to order by rand(seed) field or custom field

        }
        global $custom_products_sort_string;
        if (!empty($custom_products_sort_string)) 
            $sort_string = $custom_products_sort_string;
    }
    else {
        $sort_string = "$tables[products].product";
    }

    if(!empty($data['sort_condition'])) {
        $sort_string = $data['sort_condition'];
    }

/*
    if (($current_area == "C" || $current_area == "B") && $config['General']['disable_outofstock_products'] == "Y") {
        $query_joins['products_warehouses_amount'] = array(
            'tblname' => 'products_warehouses_amount',
            'on' => "$tables[products_warehouses_amount].product_id = $tables[products].product_id and $tables[products_warehouses_amount].variant_id=$tables[product_variants].variant_id and $tables[products_warehouses_amount.avail > 0",
            'only_select' => 0,
        );
    }
*/

    $groupbys[] = "$tables[products].product_id";
    $orderbys[] = $sort_string;
    $orderbys[] = "$tables[products].product ASC";

    // Last opportunity for addons to tweak search query
    cw_event('on_prepare_search_products', array($params, &$fields, &$from_tbls, &$query_joins, &$where, &$groupbys, &$having, &$orderbys));

    if (isset($params['product_id_only']) && $params['product_id_only'] == true) $fields = array($tables['products'].'.product_id');

    $search_query_count = cw_db_generate_query('count(*)',  $from_tbls, $query_joins, $where, $groupbys, $having, array(), 0);

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    
    if ($data['query_only']) return $search_query;
    
//var_dump($search_query);
    if (!$data['limit'] && !$data['all']) {
        
        $_res = db_query($search_query_count);
        $total_items = db_num_rows($_res);
        db_free_result($_res);

    }

# kornev, optimization, we don't need to pre-calculate the amount in some cases
    $page = $data['page'];
    if ($data['count'])
        return $total_items;
    elseif($data['limit'])
        $limit_str = " LIMIT $data[limit]";
    elseif ($data['all'])
        $limit_str = '';
    else {
        $navigation = cw_core_get_navigation($target, $total_items, $page);
        $_objects2load = $navigation['objects_per_page'];
        $_first_object_on_page = $navigation['first_page'];
        if (isset($navigation['preload_objects'])) {
            $_objects2load += $navigation['preload_objects'];
            $_first_object_on_page = max($_first_object_on_page-$navigation['preload_objects'], 0);
        }
        $limit_str = " LIMIT $_first_object_on_page, $_objects2load";
    }



    $products = $pfr = array();

    if ($params['count_only']) return array($products, $navigation, $pfr);

    if ($total_items > 0 || $data['limit'] || $data['all'] || $data['attributes']) {
        
        // Save current search query
        global $products_search_query;
        $products_search_query = $search_query;

        $products = cw_query($search_query.$limit_str);

        if ($data['limit'] || $data['all']) {
            $items_per_page = 20;

            if (!empty($objects_per_page)) {
                $items_per_page = $objects_per_page;
            }
            $navigation = cw_core_get_navigation($target, count($products), $page, $items_per_page);
        }

/* kornev, not used now
        if ($data['query_manufacturers'] == 'Y') {
            $query_joins['manufacturers'] = array(
                'on' => "$tables[products].manufacturer_id=$tables[manufacturers].manufacturer_id",
                'is_inner' => 1,
            );

            unset($where['manuf_condition']);
            $manuf_query = cw_db_generate_query(array("$tables[manufacturers].manufacturer", "$tables[manufacturers].manufacturer_id"), $from_tbls, $query_joins, $where, array("$tables[manufacturers].manufacturer"), $having, array("$tables[manufacturers].orderby"));

            $p_manufacturers = cw_query($manuf_query);
            if (is_array($p_manufacturers) && is_array($data['manufacturers']))
            foreach($p_manufacturers as $k=>$v)
                $p_manufacturers[$k]['selected'] = in_array($v['manufacturer_id'], array_keys($data['manufacturers']));
            $smarty->assign('product_manufacturers', $p_manufacturers);
        }
*/

        $pf_cache_key = substr($search_query,0,strpos($search_query,'ORDER BY'));
        //echo $pf_cache_key.' '.md5(serialize(array($pf_cache_key)));
        if (($info_type & 1024) && !($pfr=cw_cache_get(array($pf_cache_key),'PF_search'))) {
            $pfr = cw_call('cw_product_filter_build', array($data, $from_tbls, $query_joins, $where));
            cw_cache_save($pfr,array($pf_cache_key),'PF_search');
        }

        
    }

    if ($products) {
        $cart = &cw_session_register('cart', array());
        cw_load('taxes', 'warehouse', 'cart');
        global $accl;

        cw_call('on_prepare_products_found', array(&$products, $data, $user_account, $info_type));

        $ids = array();
        $ids_options = array();
        foreach ($products as $k => $v) {
            $ids[] = $v['product_id'];
            $ids_options[$v['product_id']] = doubleval($v['price']);
        }

# kornev, TOFIX
        if ($addons['product_options']) {
            $options_markups = array();
            if ($ids_options)
                $options_markups = cw_get_default_options_markup_list($ids_options);
            unset($ids_options);
        }

//        $is_assigned = ($user_account['customer_id'] && cw_warehouse_is_warehouse_assigned($user_account['customer_id']));

        foreach ($products as $k => $v) {

//            if($current_area == 'C' && $is_assigned)
            if ($current_area == 'C')
                $products[$k]['avail'] = cw_call('cw_warehouse_get_avail_for_customer', [$v['product_id'], $v['variant_id']]);
            elseif($current_area == 'A') {
                $products[$k]['avails'] = cw_warehouse_get_avails($v['product_id']);
            }

            $products[$k]['display_price'] = $v['display_price'] = $v['price'];

# kornev, TOFIX
            if ($addons['product-Options'] && !empty($v['is_product_options']) && !empty($options_markups[$v['product_id']])) {
                $products[$k]['price'] += $options_markups[$v['product_id']];
                $products[$k]['display_price'] = $products[$k]['price'];
                $v = $products[$k];
            }

            $in_cart = 0;
            if (in_array($current_area, array('C', 'G')) &&  !empty($cart['products']) && is_array($cart['products'])) {
                foreach ($cart['products'] as $cv) {
                    if ($cv['product_id'] == $v['product_id'] && intval($v['variant_id']) == intval($cv['variant_id']))
                        $in_cart += $cv['amount'];
                }

                $products[$k]['in_cart'] = $in_cart;
                $products[$k]['avail'] -= $in_cart;
                if ($products[$k]['avail'] < 0)
                    $products[$k]['avail'] = 0;
            }

            if ($info_type & 128)
                $products[$k]['image_thumb'] = cw_image_get('products_images_thumb', $v['product_id']);

            if (in_array($current_area, array('G', 'C')) && $info_type & 8) {
                $_tmp_price = $products[$k]['price'];
                $products[$k]['price'] = $products[$k]['list_price'];
                cw_get_products_taxes($products[$k], $user_account, false, '', ($current_area == 'G' && $user_account['usertype'] != 'R'));
                $products[$k]['list_price'] = $products[$k]['display_price'];
                $products[$k]['price'] = $_tmp_price;

                $products[$k]['taxes'] = cw_get_products_taxes($products[$k], $user_account, false, '', ($current_area == 'G' && $user_account['usertype'] != 'R'));
            }

            if ($products[$k]['descr'] == strip_tags($products[$k]['descr']))
                $products[$k]['descr'] = str_replace("\n", "<br />", $products[$k]['descr']);
            if ($products[$k]['fulldescr'] == strip_tags($products[$k]['fulldescr']))
                $products[$k]['fulldescr'] = str_replace("\n", "<br />", $products[$k]['fulldescr']);

        }

    }

    return array($products, $navigation, $pfr);
}

function cw_product_update_status($product_id, $status) {
    global $tables;

    if (is_array($status)) if (in_array(1,$status)) $status=1; else $status=0;

    cw_array2update('products', array('status'=>$status), "product_id = '$product_id'");
}

function cw_product_get_quick_price($product_id, $variant_id, $membership_id) {
    global $tables;

    return cw_query_first_cell("select min(price) from $tables[products_prices] where product_id='$product_id' and variant_id='$variant_id' and membership_id in (0, '$membership_id')");
}

# kornev
# info_type - used bit mask
# 00000000 00000000 (0) - standart
# 00000000 00000001 (1) - select memberships
# 00000000 00000010 (2) - status
# 00000000 00000100 (4) - always select
# 00000000 00001000 (8) - taxes info
# 00000000 00010000 (16) - system info
# 00000000 00100000 (32) - rating info
# 00000000 01000000 (64) - variant info
# 00000000 10000000 (128) - thumbnails
# 00000001 00000000 (256) - small auto generated image
# 00000010 00000000 (512) - det images
# 00000100 00000000 (2048) - Feature comparison
# 00001000 00000000 (4096) -    not used
# 00010000 00000000 (8192) - avails
# 11111111 11111111 (65535) - full information

function cw_product_get($params, $return = null) {
    extract($params);
    global $customer_id, $customer_id_type, $current_area, $cart, $current_location;
    global $current_language, $tables, $config, $addons;

    cw_load('files', 'taxes', 'tags');

    $lang = $lang?$lang:$current_language;
    $variant_id = $variant_id?$variant_id:0;
    $amount = intval($amount>0?$amount:1);

    $fields = $from_tbls = $query_joins = $where = array();
# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $from_tbls[] = 'products';
    $where[] = "$tables[products].product_id='$id'";

# kornev
# customer area - all of the checkings
# pos area - not check for membership, just available for sale property
    if (in_array($current_area, array('C'))) {
        $memberships = array(0);
        if ($user_account['membership_id']>0) $memberships[] = intval($user_account['membership_id']);
            
        $where[] = "$tables[products_memberships].membership_id IN (".join(',',$memberships).")";
        $where[] = "$tables[products_prices].quantity <= $amount and $tables[products_prices].membership_id in (".join(',',$memberships).")";

        if ($config['Appearance']['categories_in_products'] == '1') {
            $where[] = "$tables[categories_memberships].membership_id IN (".join(',',$memberships).")";

            $query_joins['products_categories'] = array(
                'on' => "$tables[products_categories].product_id = $tables[products].product_id",
                'pos' => '0',
                'is_straight' => 1,
            );
            $query_joins['categories'] = array(
                'on' => "$tables[products_categories].category_id = $tables[categories].category_id",
                'parent' => 'products_categories',
            );
            $query_joins['categories_memberships'] = array(
                'on' => "$tables[categories_memberships].category_id = $tables[categories].category_id",
                'parent' => 'categories',
                'is_straight' => 1,
            );
        }

        $where[] = "$tables[products].status in ('".implode("', '", cw_core_get_required_status($current_area))."')";

    }
    elseif (in_array($current_area, array('G')))
        $where[] = "$tables[products].product_id = $tables[products_prices].product_id AND $tables[products_prices].quantity <= $amount and $tables[products_prices].membership_id in (".join(',',$memberships).")";

    $fields[] = "$tables[products].*";

    $query_joins['products_warehouses_amount'] = array(
        'on' => "$tables[products].product_id = $tables[products_warehouses_amount].product_id and $tables[products_warehouses_amount].warehouse_customer_id=0 and $tables[products_warehouses_amount].variant_id='$variant_id'",
    );

    $in_cart = 0;
    if ($current_area == 'C' && !empty($cart) && !empty($cart['products'])) {
        foreach ($cart['products'] as $cart_item)
        if ($cart_item['product_id'] == $id)
            $in_cart += $cart_item['amount'];
    }
    $fields[] = "$tables[products_warehouses_amount].avail-$in_cart AS avail";

# kornev, TOFIX
    if ($addons['product_options'] && (in_array($current_area, array('A', 'P')))) {
        $query_joins['product_variants'] = array(
            'on' => "$tables[products].product_id = $tables[product_variants].product_id",
        );
        $fields[] = "IF($tables[product_variants].product_id IS NULL, '', 'Y') as is_variants";
    }

/*
    if ($addons['manufacturers']) {
        $query_joins['manufacturers'] = array(
            'on' => "$tables[manufacturers].manufacturer_id = $tables[products].manufacturer_id",
        );
        $fields[] = "$tables[manufacturers].manufacturer";
    }
*/
    // statistic
    $fields[] = "$tables[products_stats].views_stats";
    $fields[] = "$tables[products_stats].sales_stats";
    $fields[] = "$tables[products_stats].del_stats";
    $fields[] = "$tables[products_stats].add_to_cart";
    $query_joins['products_stats'] = array(
        'on' => "$tables[products_stats].product_id = $tables[products].product_id",
    );

    if ($current_area == 'A' || $current_area == 'P') {
        $fields[] = "$tables[products_prices].price";
        $fields[] = "$tables[products_prices].list_price";

        $query_joins['products_prices'] = array(
            'on' => "$tables[products_prices].product_id=$tables[products].product_id AND $tables[products_prices].variant_id = '$variant_id' and $tables[products_prices].quantity <= $amount",
        );
    }
    else {
        $query_joins['products_prices'] = array(
            'on' => "$tables[products_prices].product_id=$tables[products].product_id",
            'is_inner' => 1,
        );
# kornev, find the min price and select only this record.
        $fields[] = "min($tables[products_prices].price) as price";
        $fields[] = "$tables[products_prices].variant_id";
        $fields[] = "min($tables[products_prices].list_price) as list_price";
    }

    $fields[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].product, $tables[products].product) as product";
    $fields[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].descr, $tables[products].descr) as descr";
    $fields[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].fulldescr, $tables[products].fulldescr) as fulldescr";
    $fields[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].features_text, $tables[products].features_text) as features_text";
    $fields[] = "IF($tables[products_lng].product_id != '', $tables[products_lng].specifications, $tables[products].specifications) as specifications";

    $query_joins['products_lng'] = array(
        'on' => "$tables[products_lng].code='$lang' AND $tables[products_lng].product_id = $tables[products].product_id",
    );

    if (in_array($current_area, array('C', 'G', 'S'))) {
        $fields[] = "$tables[products_flat].*";

        $query_joins['products_flat'] = array(
            'on' => "$tables[products].product_id = $tables[products_flat].product_id",
        );

        if ($current_area == 'C') {
            $query_joins['products_memberships'] = array(
                'on' => "$tables[products_memberships].product_id = $tables[products].product_id",
                'is_inner' => 1,
            );
        }
    }

    if ($config['Appearance']['categories_in_products'] == '1') {
        $fields[] = "$tables[products_categories].category_id";
        $query_joins['categories'] = array(
            'parent' => 'products_categories',
            'on' => "$tables[categories].category_id = $tables[products_categories].category_id",
        );
        $query_joins['products_categories'] = array(
            'on' => "$tables[products_categories].product_id = $tables[products].product_id and $tables[products_categories].main=1",
                'pos' => '0',
                'is_straight' => 1,
        );
    }

    $fields[] = "$tables[products].product_id";
    $query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, array("$tables[products].product_id"), array(), array());

    $product = cw_query_first($query);

    # Error handling
    if (
        !$product
        || (
            $current_area == 'C'
            && !$product['category_id']
            && $config['Appearance']['categories_in_products'] == '1'
        )
    ) {
        return false;
    }

    $product['system'] = cw_call('cw_product_get_system_info', array($product['product_id']));

    $product['attribute_class_ids'] = cw_func_call('cw_items_attribute_classes_get', array('item_id'=>$product['product_id'], 'item_type' => 'P', 'for_product_modify' => $for_product_modify)); 

    if ($info_type & 1)
        $product['membership_ids'] = cw_query_key("select membership_id from $tables[products_memberships] where product_id = '$product[product_id]'");

# kornev, TOFIX
    if ($info_type & 64 && $product['variant_id'] && $addons['product_options']) {
        $tmp = cw_query_first("SELECT * FROM $tables[product_variants] WHERE variant_id = '$product[variant_id]'");
        if (!empty($tmp)) {
            cw_unset($tmp, "def");
            $product = cw_array_merge($product, $tmp);
        }
        else
            cw_unset($product, 'variant_id');
    }

    if ($info_type & 128) {
        cw_load('image');
        $product['image_thumb'] = cw_image_get('products_images_thumb', $id);
    }

    if ($info_type & 512) {
        $product['image_det'] = cw_image_get('products_images_det', $id);

# kornev, TOFIX
        if (in_array($current_area, array('C', 'B')) && $product['variant_id'] && $addons['product_options']) {
            $var_image = cw_image_get('products_images_var', $id);
            if (!$var_image['is_default']) $product['image_det'] = $var_image;
        }
    }

# TOFIX
    if (in_array($current_area, array('C', 'B', 'G'))) {
        if (!$addons['egoods'])
            $product['distribution'] = '';

        $product['display_price'] = $product['price'];

        if ($current_area == 'C' && $info_type & 8) {
            $_tmp_price = $product['price'];
            $product['price'] = $product['list_price'];
            cw_get_products_taxes($product, $user_account);
            $product['list_price_net'] = $product['list_price'];
            $product['list_price'] = $product['taxed_price'];
            $product['price'] = $_tmp_price;
        }
        $product['taxes'] = cw_get_products_taxes($product, $user_account, false, '', ($current_area == 'G' && $user_account['usertype'] != 'R'));
    }

    if (in_array($current_area, array('C', 'B'))) {
        $product['descr'] = cw_eol2br($product['descr']);
        $product['fulldescr'] = cw_eol2br($product['fulldescr']);
    }

    $product['uns_shippings'] = unserialize($product['shippings']);
    $product['tags'] = cw_tags_get_product_tags($id);

    // TODO: move to addon as on_product_get handler
    if ($info_type & 8192) {
        cw_load('warehouse');
        if ($addons['warehouse']) {
            if (AREA_TYPE == 'A') {
                $product['avail_ordered'] = cw_warehouse_get_avail($id, 0, 'avail_ordered');
                $product['avail_sold'] = cw_warehouse_get_avail($id, 0, 'avail_sold');
                $product['avail_reserved'] = cw_warehouse_get_avail($id, 0, 'avail_reserved');
            }
            elseif (AREA_TYPE == 'P') {
                $product['avail'] = cw_warehouse_get_warehouse_avail($customer_id, $id);
                $product['avail_ordered'] = cw_warehouse_get_warehouse_avail($customer_id, $id, 'avail_ordered');
            }
            else {
// TOFIX:  $product['avail'] becomes different meanings when info_type & 8192 flag is raised or not
// without flag - avail of all variants without products already in cart
// with flag - total avail as set in admin per variant
//                $product['avail'] = cw_warehouse_get_avail_for_customer($id, $product['variant_id']);
//                $product['avails']= cw_warehouse_get_avails_customer($id);
                $product['avail'] = cw_call('cw_warehouse_get_avail_for_customer', [$id, $product['variant_id']]);
            }
        }
        else
            $product['avails']= cw_warehouse_get_avails_customer($id, $product['avail']+$product['avail_ordered']);
    }

    return $product;
}

#
# Get delivery options by product ID
#
function cw_select_product_delivery($id) {
    global $tables;

    return cw_query("select $tables[shipping].*, count($tables[products_shipping].product_id) as avail from $tables[shipping] left join $tables[products_shipping] on $tables[products_shipping].shipping_id=$tables[shipping].shipping_id and $tables[products_shipping].product_id='$id' where $tables[shipping].active=1 group by shipping_id");
}

#
# Get stop words list
#
function cw_get_stopwords($code = false) {
    global $app_main_dir, $current_language;

    if ($code === false)
        $code = $current_language;

    if (!file_exists($app_main_dir."/include/stopwords_".$code.".php"))
        return false;

    $stopwords = array();
    include $app_main_dir."/include/stopwords_".$code.".php";

    return $stopwords;
}

function cw_insert_product_to_sections($product_id, $ins_sections) {
    global $tables;

    if (is_array($ins_sections)) {
    foreach($ins_sections as $section=>$val) {
        db_query("delete from ".$tables[$section]." where product_id='$product_id'".($section == 'featured_products'?" and category_id=0":""));
        if ($val['insert_to_section'] != 'Y') continue;
        unset($val['insert_to_section']);
        $val['product_id'] = $product_id;
        $val['from_time'] = cw_core_strtotime($val['from_time']);
        $val['to_time'] = cw_core_strtotime($val['to_time']);
        if ($section == 'featured_products') {
            $val['category_id'] = 0;
            if (!isset($val['avail']) || $val['avail']!=1) $val['avail']=0;
        }
        else
            $val['active'] = 1;
        cw_array2insert($section, $val);
    }
    }
}

function cw_product_get_types() {
    global $tables;

    return cw_query("select * from $tables[products_types]");
}

function cw_product_get_product_by_ean($ean) {
    global $tables, $user_account;

    $info = cw_ean_get_product_info($ean);
    return cw_func_call('cw_product_get', array('id' => $info['product_id'], 'user_account' => $user_account));
}

function cw_product_generate_sku($ean_type = 0, $field = 'productcode', $prefix = 'PC') {
    global $tables, $app_main_dir;

    if ($ean_type == 'ean13') $cols = 13;
    elseif ($ean_type == 'ean8') $cols = 8;
    else $cols = intval($ean_type);

    do {
        if (empty($cols)) $code = $prefix.cw_core_generate_string(8);
        else {
            $code = cw_core_generate_string($cols-1, false);
            if (in_array($ean_type, array('ean13', 'ean8'), true)) {
                include_once $app_main_dir.'/include/barcode/BarCode.php';
                include_once $app_main_dir.'/include/barcode/FColor.php';
                include_once $app_main_dir.'/include/barcode/FDrawing.php';
                include_once $app_main_dir.'/include/barcode/'.$ean_type.'.BarCode.php';
                $code_obj = new $ean_type($code);
                $code = $code_obj->getFull();
            }
        }
        $is_ex = cw_query_first_cell("select count(*) from $tables[products] where $field='$code'");
    }
    while($is_ex);

    return $code;
}

function cw_product_update_system_info($product_id, $data) {
    global $tables, $customer_id;

    $time = cw_core_get_time();
    $update = array('modification_customer_id' => $customer_id, 'modification_date' => $time);

    if (!is_array($data) && is_numeric($data)) {
        // $data contains single customer_id
        $data = array('modification_customer_id' => intval($data), 'modification_date' => $time);
    }

    $update = array_merge($update, $data);
    
    if (!cw_query_first_cell("select count(*) from $tables[products_system_info] where product_id='$product_id'")) {
        cw_array2insert('products_system_info', array('product_id' => $product_id, 'creation_customer_id' => intval($update['modification_customer_id']), 'creation_date' => $time), true); 
    }

    cw_array2update('products_system_info', $update, "product_id='$product_id'");
}

function cw_product_get_system_info($product_id) {
    global $tables;
    return cw_query_first("select * from $tables[products_system_info] where product_id='$product_id'");
}

function cw_product_clone($product_id) {
    global $addons, $customer_id, $tables;

    $tables_array = array(
        array('table' => 'products_images_thumb',       'key_field' => 'id'),
        array('table' => 'products_images_det',         'key_field' => 'id'),
        array('table' => 'delivery',                    'key_field' => 'product_id'),
        array('table' => 'attributes_values',           'key_field' => 'item_id'),
        array('table' => 'products_lng',                'key_field' => 'product_id'),
        array('table' => 'products_categories',         'key_field' => 'product_id'),
        //array('table' => 'products_taxes',                'key_field' => 'product_id'),
        array('table' => 'products_memberships',        'key_field' => 'product_id'),
    );

    $product_data = cw_query_first("SELECT * FROM $tables[products] WHERE product_id='$product_id'");
    if (!$product_data) return;

    $product_data['productcode'] = cw_product_generate_sku();
    $new_product_id = cw_array2insert(
                        'products',
                        array(
                            'productcode'  => $product_data['productcode'],
                            'product_type' => $product_data['product_type']
                        ));
    $to_update = array();

    foreach ($product_data as $field => $value) {
        if (!in_array($field, array('product_id', 'productcode', 'product_type', 'views_stats'))) {
            $to_update[] = $field;
        }
    }
    $product_data['product'] = $product_data['product'].' (CLONE)';
    cw_array2update('products', cw_addslashes($product_data), "product_id='$new_product_id'", $to_update);

    foreach ($tables_array as $k=>$v) {
        $error_string .= cw_core_copy_tables($v['table'], $v['key_field'], $product_id, $new_product_id);
    }

    db_query("update $tables[products_lng] set product = concat(product, ' (CLONE)') where product_id='$new_product_id'");

    // Clone prices
    $prices = cw_query("SELECT * FROM $tables[products_prices] WHERE product_id = '$product_id' AND variant_id = '0'");
    if (!empty($prices)) {
        foreach ($prices as $v) {
            unset($v['price_id']);
            $v['product_id'] = $new_product_id;
            cw_array2insert('products_prices', $v);
        }
    }

    $prices = cw_query("select pp.* from $tables[products_prices] as pp where pp.product_id='$product_id'");

    cw_func_call('cw_product_build_flat', array('product_id' => $new_product_id));
    cw_product_update_system_info($new_product_id, $customer_id);
//    cw_warehouse_recalculate($product_id);

    // Update products counter for categories in which product is placed
    $product_categorie = cw_query_first("SELECT category_id FROM $tables[products_categories] WHERE product_id = '$product_id'");
    cw_recalc_subcat_count($product_categorie['category_id']);

    return $new_product_id;
}

function cw_product_get_sort_fields() {
    return array(
    'productcode'   => cw_get_langvar_by_name('lbl_sku'),
    'title'         => cw_get_langvar_by_name('lbl_product'),
    'price'         => cw_get_langvar_by_name('lbl_price'),
    'orderby'       => cw_get_langvar_by_name('lbl_default'),
    );
}

function cw_product_get_price_id($product_id, $variant_id=0, $membership_id=0, $quantity=1) {
    global $tables;

    $price_id = cw_query_first_cell($sql="select price_id from $tables[products_prices] where product_id='$product_id' and variant_id='$variant_id' and membership_id='$membership_id' and quantity='$quantity'");

    if (!$price_id)
        $price_id = cw_array2insert('products_prices', array('product_id' => $product_id, 'variant_id' => $variant_id, 'membership_id' => $membership_id, 'quantity' => $quantity));

    return $price_id;
}

function cw_product_replace_price($product_id, $price, $variant_id = 0, $is_new = false) {
    global $tables;

    $price_id = cw_product_get_price_id($product_id, $variant_id);

    if ($variant_id && empty($price)) {
        $default_price_id = cw_product_get_price_id($product_id);
        $price = cw_query_first_cell("select price from $tables[products_prices] where price_id='$default_price_id'");
    }

    $to_update = array(
        'price' => abs($price),
    );
    cw_array2update('products_prices', $to_update, "price_id='$price_id'");
# kornev, if there is any variant price - we should remove the product one.
    if ($variant_id) db_query("delete from $tables[products_prices] where product_id='$product_id' and variant_id=0");
}

function cw_product_update_price($product_id, $variant_id, $membership_id, $membership_id_old, $quantity, $quantity_old, $price, $list_price, $price_id = 0) {
    global $tables;

    if (!$price_id) $price_id = cw_product_get_price_id($product_id, $variant_id, $membership_id_old, $quantity_old);

    if ($quantity <= 0) return;

    $to_insert = array(
        'price_id' => $price_id,
        'product_id' => $product_id,
        'variant_id' => $variant_id,
        'membership_id' => $membership_id,
        'quantity' => $quantity,
        'price' => $price,
        'list_price' => $list_price,
    );
    cw_array2insert('products_prices', $to_insert, true);
# kornev, if there is any variant price - we should remove the product one.
    if ($variant_id) db_query("delete from $tables[products_prices] where product_id='$product_id' and variant_id=0");
}

function cw_product_get_filter_location($product_filter, $ns) {
    global $config;

    $return = array();

    if ($product_filter)
    foreach($product_filter as $att)
        if ($att['is_selected']) {
            $url = cw_call('cw_clean_url_get_seo_url',array(cw_func_call('cw_product_navigation_filter_url', array('ns' => $ns, 'att_id' => $att['attribute_id'], 'value_selected' => $att['selected'], 'is_selected' => 1))));
            if (in_array($att['pf_display_type'], array('S', 'R'))) {
                if ($att['is_price'])
                    $return[] = array(cw_add_filter_location_container_name($att['name']).': '.$config['General']['currency_symbol'].$att['selected']['min_name'].' - '.$config['General']['currency_symbol'].$att['selected']['max_name'], $url, 'delete-filter');
                else
                    $return[] = array(cw_add_filter_location_container_name($att['name']).': '.$att['selected']['min'].' - '.$att['selected']['max'], $url, 'delete-filter');
            }
            else {
                foreach($att['selected'] as $v)
                    if ($att['type'] == 'yes_no')
                        $return[] = array(cw_add_filter_location_container_name($att['name']).': '.cw_get_langvar_by_name($v?'lbl_yes':'lbl_no'), $url, 'delete-filter');
                    else {
# kornev, new structure - the name is stored differently
# kornev, also we need the image...
                        $str = '';
# kornev, TOFIX - optimize, generally a few items only - but problem is possible
                        foreach($att['values'] as $att_val)
                            if ($att_val['id'] == $v) {
                                if (in_array($att['pf_display_type'], array('W', 'E', 'G')) && $att_val['image']) $str .= '<img src="'.$att_val['image']['tmbn_url'].'"  alt="" width="20" />';
                                if (in_array($att['pf_display_type'], array('E', 'T','P'))) $str .=  ' '.$att_val['name'];
                                break;
                            }
                        $return[] = array(cw_add_filter_location_container_name($att['name']).': '.($str?trim($str):$v), $url, 'delete-filter');
                    }
            }
        }
    return $return;
}

function cw_add_filter_location_container_name($name) {
    return "<span class='filter_location_container_name'>$name</span>";
}

function cw_product_navigation_filter_url($params) {
    if (!$params['ns']) return '';

    if (!$params['is_selected']) return $params['ns'] . '&att[' . $params['att_id'] . '][]=' . $params['value_id'];

    if (!$params['value_selected']) return $params['ns'];

    $url_info = parse_url($params['ns']);
    parse_str($url_info['query'], $arr);

    if (!isset($arr['att'][$params['att_id']])) return $params['ns'];

    if (is_array($arr['att'][$params['att_id']]))
        foreach($arr['att'][$params['att_id']] as $k=>$v)  {
            if (in_array($v, $params['value_selected'])) unset($arr['att'][$params['att_id']], $k);
        }
    else
        unset($arr['att'][$params['att_id']]);

    return $url_info['path'].'?'.http_build_query($arr);
}

function cw_product_get_filter_replaced_url($product_filter, $ns) {
    $url = trim($ns, '?');
    $arr = array();

    if ($product_filter) {
        foreach ($product_filter as $att) {
            if ($att['selected']) {
                foreach ($att['selected'] as $s_value) {
                    if (isset($att['values'][$s_value]['facet']) && $att['values'][$s_value]['facet'] == 1) {
                        $arr['att'][$att['attribute_id']][0] = $s_value;
                    }
                }
            }
        }
        if ($arr) {
            $url = cw_func_call(
                'cw_product_navigation_filter_url',
                array(
                    'ns' => $ns . (strpos($ns,'?')!==false ? '&' : '?') . http_build_query($arr),
                    'att_id' => 0,
                    'value_selected' => 0,
                    'is_selected' => 0
                )
            );
        }
    }

    return $url;
}

# kornev, params
# $params[product_id]
# $params[tick]
function cw_product_build_flat($params, $return) {
    extract($params);

    global $tables, $addons;

    $where = "";
    if ($product_id) {
        if (!is_array($product_id)) $product_id = array($product_id);
        $where = "product_id in ('".implode("', '", $product_id)."')";
        db_query("delete from $tables[products_flat] where $where");
    }
    else
        db_query("delete from $tables[products_flat]");

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $from_tbls[] = 'products';
    $fields[] = "$tables[products].product_id";
    $where[] = $tables['products'].'.'.$where;

# kornev, get the query from the addons
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

# kornev, there are nothing to do if the fields are empty (by default)
    if (count($fields) == 1) return;

    $groupbys[] = "$tables[products].product_id";
# kornev, generate it;
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    if ($tick > 0)
        cw_display_service_header('lbl_rebuild_products_flat');

    $sd = db_query($search_query);

    $updated = 0;
    if ($sd)
    while ($row = db_fetch_array($sd)) {
        cw_array2insert('products_flat', cw_addslashes($row), true);
        $updated++;
        if ($tick > 0 && $updated % $tick == 0) cw_flush('.');
        if ($tick > 0  && ($updated/$tick) % 100 == 0) cw_flush('<br/>');
    }
    db_free_result($sd);

    return $updated;
}

# kornev, params
# params[product]
function cw_product_check_avail($params, $return) {
    global $config, $cart;

    if ($return === true) return true;

    $is_avail = true;
    if ($params['product']['avail'] <= 0)
        $is_avail = false;

    if(!empty($cart['products']) && !$is_avail)
    foreach($cart['products'] as $v) {
        if($params['product']['product_id'] == $v['product_id']) {
            $is_avail = true;
            break;
        }
    }

    return $is_avail;
}

/* run product counter
 *
 * counter types
 * 0 - views_stats
 * 1 - sales_stats
 * 2 - del_stats
 * 3 - add_to_cart
 * */
function cw_product_run_counter($product_id, $count, $type) {
    global $tables, $config;

    if (
        empty($product_id)
        || empty($count)
        || !is_numeric($type)
        || $type < 0
        || $type > 3
        || ($config['performance']['enable_product_stats'] != 'Y')
    ) {
        return FALSE;
    }

    $types = array('views_stats', 'sales_stats', 'del_stats', 'add_to_cart');

    db_query("INSERT DELAYED INTO $tables[products_stats] (product_id, {$types[$type]}) values ($product_id, $count)
              ON DUPLICATE KEY UPDATE {$types[$type]} = ({$types[$type]} + '$count') ");

    return TRUE;
}


function _sort_pf_by_counter($a, $b) {
    if (intval($a['orderby']) == intval($b['orderby']))
        return $a['counter']<$b['counter'];
    else 
        return $a['orderby']>$b['orderby'];
} 

function _sort_pf_by_pf_orderby($a, $b) {
    if (intval($a['pf_orderby']) == intval($b['pf_orderby']))
        return $a['orderby']>$b['orderby'];
    else 
        return $a['pf_orderby']>$b['pf_orderby'];
}
