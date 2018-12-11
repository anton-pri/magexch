<?php
# kornev,
# $params['is_price']
# $params['value']
# $params['user_account']
# $params[ current_area']
function cw_product_filter_get_slider_value($params, $return) {
    extract($params);
    if (!$is_price) return $value;

    $pftmp = array('product_id' => 0, 'price' => $value);
    $taxes = cw_get_products_taxes($pftmp, $user_account, false, '', ($current_area == 'G' && $user_account['usertype'] != 'R'));
    return $pftmp['display_price'];
}

function cw_product_filter_get_price_ranges($attribute_id, $price_values = null){
global $config, $tables;

         $values =  cw_call('cw_attributes_get_attribute_default_value',array($attribute_id));
         $ranges = array();
         foreach($values as $v){
             $pr = explode('-',$v['value_key']);
             $range['counter'] = 0;
             if(is_array($price_values))
                 foreach($price_values as $k => $p)
                     if($k>=$pr[0] && $k <= $pr[1])
                         $range['counter'] += $p['counter'];
             if($range['counter']==0 || $v['active']==0) continue;


             $range['id']    = $v['attribute_value_id'];
             $range['url']   = cw_query_first_cell("SELECT value FROM $tables[attributes_values] WHERE item_id = '$v[attribute_value_id]'");
             $range['value'] = $v['value_key'];
             if ($config['product']['pf_show_ranges_values'] == "Y") 
                 $range['name']  = $v['value'].'('.$config['General']['currency_symbol'].str_replace('-',' - '.$config['General']['currency_symbol'],  $v['value_key']).')';
             else   
                 $range['name']  = $v['value'];


             array_push($ranges, $range);

         }
    return $ranges;
}

function cw_product_filter_get_price_range_values($attribute_value_id){
    global $tables;

    $range = cw_query_first_cell("SELECT value_key FROM $tables[attributes_default] WHERE attribute_value_id = $attribute_value_id");
    $range = explode('-', $range);

    return (count($range)==2 ? $range : null);
}

function cw_product_filter_recalculate_price_ranges(){
    global $tables;

    $price_ranges = cw_product_filter_get_price_ranges(PRICE_ATTRIBUTE_ID);
    if ($price_ranges) {
        $min_price = cw_query_first_cell("SELECT MIN(price) FROM $tables[products_prices]");
        $max_price = cw_query_first_cell("SELECT MAX(price) FROM $tables[products_prices]");
        $min['id'] = $min['val'] = $max['id'] = $max['val'] = 0;
        foreach($price_ranges  as  $pr){
            $r = cw_product_filter_get_price_range_values($pr['id']);
            if($min_price > $r[1] || $max_price < $r[0]){
                cw_array2update('attributes_default',array('active'=> 0), "attribute_value_id='$pr[id]'");
                continue;
            }
                $price_ranges[$pr['id']]['min'] = $r[0];
                $price_ranges[$pr['id']]['max'] = $r[1];
            if($min['id']==0){
                $min['id'] = $max['id'] = $pr['id'];
                $min['val'] = $r[0];
                $max['val'] = $r[1];
            }else{
                if($min['val'] > $r[0]){
                   $min['val'] = $r[0];
                   $min['id'] =  $pr['id'];
                }
                if($max['val'] < $r[1]){
                   $max['val'] = $r[1];
                   $max['id'] =  $pr['id'];
                }
            }
        }
        if($min_price!=$min['val'])
            cw_array2update('attributes_default', array('value_key' => $min_price.'-'.$price_ranges[$min['id']]['max']), "attribute_value_id='".$min['id']."'");
        if($max_price!=$max['val'])
            cw_array2update('attributes_default', array('value_key' => $price_ranges[$max['id']]['min'].'-'.$max_price), "attribute_value_id='".$max['id']."'");

    }
}

/**
 * Prepare $product_filter array.
 * Called from cw_product_search() or cw_product_filter_build_content()
 */
function cw_product_filter_build($data, $from_tbls, $query_joins, $where) {
    global $tables, $config, $current_language, $current_area, $user_account;
    

    $pfr = array();
    # kornev, we need that for the breadcrumbs
    if ($data['attributes']['substring']) {
        $pfr[] = array(
        'attribute_id' => 'substring',
        'name' => cw_get_langvar_by_name('lbl_substring'),
        'type' => '',
        'is_selected' => 1,
        'selected' => array($data['attributes']['substring']),
        'values' => array(),
        );
    }

    # kornev, firstly select all of the attributes to build the filter - without products count
    $attributes = cw_call('cw_attributes_filter',array(array(
                'item_type' => 'P',
                'pf_is_use' => 1,
                'active' => 1,
                )));
    uasort($attributes,'_sort_pf_by_pf_orderby');

    if ($attributes) {
        $att_ids = $ss_att = array();
        foreach($attributes as $k=>$v) {
            $att_ids[] = $v['attribute_id'];

            // Mark price attribute with special flag is_price
            if ($v['field'] == 'price') {
                //$attributes[$k]['attribute_id'] = $v['attribute_id'] = 'price';
                $attributes[$k]['is_price'] = 1;
            }

            // If slider attr is passed in data, then prefill special array for sliders
            if ($v['pf_display_type'] == 'S' &&  isset($data['attributes'][$v['attribute_id']])) {
                $ss_att[] = $v;
            }
        }
        $query_joins['atv'] = array(
            'tblname' => 'attributes_values',
            'on' => "$tables[products].product_id=atv.item_id and atv.attribute_id in ('".implode("', '", $att_ids)."') and atv.code in ('$current_language', '') and atv.value != ''",
    # kornev, we cannot use the inner join because of the prices
    //                    'is_inner' => 1,
        );
    # kornev, we have to select all of the slider attributes separatelly

        // general selection of all attributes, values, prices for all products according to search query
        $attribute_query = cw_db_generate_query(array("atv.attribute_id", "atv.value", 'atv.item_id', $tables['products_prices'].'.price'), $from_tbls, $query_joins, $where, null, null,null);
        
        //$atv = cw_query($attribute_query); // It takes a lot of memory and don't want return back even after unset()
        
        $tmp = $prices = array();

        if ($p_result = db_query($attribute_query, null)) {
            while ($v = db_fetch_array($p_result)) {
                
                if ($v['item_id']) {
                    $tmp[$v['attribute_id']][$v['value']][$v['item_id']] = $v['price']; // Use price (to calculate min_price later) instead of dummy flag "1" 
                }
                if ($v['price']) $tmp[PRICE_ATTRIBUTE_ID][$v['price']][$v['item_id']] = 1;
            }
            db_free_result($p_result);
        }

    # kornev, for the sliders we should select all of the values - so without the appropriate condition for the attribute;
        if ($ss_att && $config['product']['pf_is_slow_mode'])
        foreach($ss_att as $id=>$v) {
            $__query_joins = $query_joins;
            $__where = $where;
            if ($id == 'price') {
                unset($__where['price_min']);
                unset($__where['price_max']);
                $flds = array($tables['products_prices'].'.price', $tables['products_prices'].'.product_id');
            }
            else {
                unset($__query_joins['atv_'.$v['id']]);
                $flds = array('atv.attribute_id', 'atv.value', 'atv.item_id');
    //                        $__where[] = "atv_$v[id]. "
            }
            $attribute_query = cw_db_generate_query($flds, $from_tbls, $__query_joins, $__where, null, null, null);

            if ($p_result = db_query($attribute_query, null)) {
                while ($v = db_fetch_array($p_result)) {
                    
                    if ($v['item_id']) {
                        $tmp[$v['attribute_id']][$v['value']][$v['item_id']] = $v['price']; // Use price (to calculate min_price later) instead of dummy flag "1" 
                    }
                    if ($v['price']) $tmp[PRICE_ATTRIBUTE_ID][$v['price']][$v['item_id']] = 1;
                }
                db_free_result($p_result);
            }
        }

        $atv = null;
        unset($atv, $ss_att);

    # kornev, for now the fake price attribute is used - we should move it to the attributes array
    # kornev, TOFIX - move price to attributes
    //                array_unshift($attributes, array('attribute_id' => 'price', 'name' => cw_get_langvar_by_name('lbl_price'), 'type' => 'decimal', 'field' => 'price', 'pf_display_type' => 'S', 'is_price' => 1));

        foreach($attributes as $ka=>$v) {
            $images = array();
            if (!$tmp[$v['attribute_id']]) continue;
    # kornev, array_multisort problem with numbers
    # kornev, name will be replaced for selectors
            foreach($tmp[$v['attribute_id']] as $k=>$pids) {
                $v['values'][$k] = array(
                    'name' => $k, 
                    'id' => $k, 
                    'counter' => count($pids),
                    );
                    if ($config['product']['pf_show_from_price']=='Y') {
                        $v['values'][$k]['min_price'] = cw_func_call('cw_product_filter_get_slider_value', array('value' => min($pids), 'is_price' => true, 'current_area' => $current_area, 'user_account' => $user_account));
                    }
            }

            if (isset($data['attributes'][$v['attribute_id']])) {
                $v['is_selected'] = true;
                $v['selected'] = $data['attributes'][$v['attribute_id']];
            }

    # kornev, we need to apply some order by to the fields
    # kornev, also we have to add the images for the selectboxes and multi-selectboxes

            $dfv = cw_call('cw_attributes_get_attribute_default_value', array('attribute_id' => $v['attribute_id']));
            if (in_array($v['type'], array('multiple_selectbox', 'selectbox'))) {
                $sort = $images = array();
                if ($dfv) {
                    $prepared_dfv = array();
                    foreach($dfv as $k=>$d) $prepared_dfv[$d['attribute_value_id']] = $d;

                    foreach($v['values'] as $k=>$d) {
                        if (!$prepared_dfv[$k] || $prepared_dfv[$k]['active']!=1) {
                            unset($v['values'][$k]);
                            continue;
                        }
                        $sort[$k] = $prepared_dfv[$k]['counter'];
                        $v['values'][$k]['name'] = $prepared_dfv[$k]['value'];
                        $v['values'][$k]['description'] = $prepared_dfv[$k]['description'];
                        $v['values'][$k]['facet'] = $prepared_dfv[$k]['facet'];
                        $v['values'][$k]['orderby'] = $prepared_dfv[$k]['orderby'];
                        // if the type includes swatch - find the images
                        if (in_array($v['pf_display_type'], array('W', 'E', 'G'))) $images[$k] = $prepared_dfv[$k]['pf_image_id'];
                    }
    # kornev, some problems with values - TOFIX - move to attributes
    /* doesn't work
                    if (count($sort) != count($v['values']))
                    foreach($v['values'] as $k=>$d)
                        if (!isset($sort[$k]))
                            db_query("delete from $tables[attributes_values] where attribute_id='$v[attribute_id]' and value='".addslashes($k)."'");
    */

                }
            } else {
                if (isset($dfv[0]) && $dfv[0]) {
                    foreach ($v['values'] as $k => $d) {
                        $v['values'][$k]['description'] = $dfv[0]['description'];
                        $v['values'][$k]['facet'] = $dfv[0]['facet'];
                    }
                }
            }

            uasort($v['values'],'_sort_pf_by_counter');
            

    # kornev, if slider is used - limit the number of chunks to 50
            if (in_array($v['pf_display_type'], array('S', 'R'))) {
    # kornev, slider should be sorted in another way - as the deciaml/integer numbers (sql sorted it as a string)
                ksort($v['values']);

                $min = array_shift($v['values']);
                $max = array_pop($v['values']);
                if(!$max)
                    $max = $min;
    # kornev, slider with one value is not possible
                if (!$max && !$v['selected']) continue;
                $v['min_name'] = $v['min'] = $min['name'];
                $v['max_name'] = $v['max'] = $max['name'];

                if ($v['pf_display_type'] == 'S' && is_numeric($min['name']) && is_numeric($max['name'])) {
                    $approx_range = ($max['name'] - $min['name']) / 50;
                    $start = $min['name'];
                    $is_price = ($v['is_price'] && in_array($current_area, array('G', 'C')));
                    $values = array();
                    $values[$min['id']] = cw_func_call('cw_product_filter_get_slider_value', array('field' => $v['field'], 'value' => $min['id'], 'is_price' => $is_price, 'current_area' => $current_area, 'user_account' => $user_account));

                    $current = null;
                    while($v['values'] || $current) {
                        if (!$current) {
                            $current = array_shift($v['values']);
                            $current['name'] = ($v['type']=='integer'?intval($current['name']):floatval($current['name'])); // Foolproof if numeric fields contain text
                        }
                        else {
                            $start_prev = $start;
                            $start += $approx_range;
                        }
                        if ($start >= $current['name']) {
                            if ($current['name'] > $start_prev) {
    # kornev, we need calculate the taxes for the price;
                                $values[$current['id']] = cw_func_call('cw_product_filter_get_slider_value', array('field' => $v['field'], 'value' => $current['id'], 'is_price' => $is_price, 'current_area' => $current_area, 'user_account' => $user_account));
                                $start_prev = $start;
                                $start += $approx_range;
                            }
                            $current = null;
                        }
                    }

                    $values[$max['id']] = cw_func_call('cw_product_filter_get_slider_value', array('field' => $v['field'], 'value' => $max['id'], 'is_price' => $is_price, 'current_area' => $current_area, 'user_account' => $user_account));
                    $v['values'] = $values;
                    $v['values_counter'] = count($values);
    //                            if ($is_price) {
                    $v['min_name'] = cw_func_call('cw_product_filter_get_slider_value', array('field' => $v['field'], 'value' => $v['min'], 'is_price' => $is_price, 'current_area' => $current_area, 'user_account' => $user_account));
                    $v['max_name'] = cw_func_call('cw_product_filter_get_slider_value', array('field' => $v['field'], 'value' => $v['max'], 'is_price' => $is_price, 'current_area' => $current_area, 'user_account' => $user_account));
                    if ($v['selected'])
                    foreach($v['selected'] as $sk=>$sv) $v['selected'][$sk.'_name'] = cw_func_call('cw_product_filter_get_slider_value', array('field' => $v['field'], 'value' => $sv, 'is_price' => $is_price, 'current_area' => $current_area, 'user_account' => $user_account));
    //                            }
                }

            } elseif (in_array($v['type'], array('integer', 'decimal')) && in_array($v['pf_display_type'], array('P','E','W','G'))) {

                $v['values'] = cw_call('cw_product_filter_get_price_ranges', array('attribute_id'=>$v['attribute_id'], $v['values']));

                // Images collection
                if (in_array($v['pf_display_type'], array('W', 'E', 'G')))
                    foreach ($v['values'] as $kk=>$vv)
                        $images[$kk] = $dfv[$kk]['pf_image_id'];

                if (isset($data['attributes'][$v['attribute_id']])) {
                    $v['is_selected'] = true;
                    $v['selected'] = $data['attributes'][$v['attribute_id']];
                }
            }
    # kornev, assign the images....
    # kornev, TOFIX speed up is possible here
            if ($images)
            foreach($images as $kk=>$dd)
                $v['values'][$kk]['image'] = cw_image_get('attributes_images', $dd);
                
            $pfr[] = $v;
        } // foreach $attributes
    }
    unset($attributes, $tmp, $dfv, $prepared_dfv, $values, $images);
    
    return $pfr;
        
}

/**
 * Store in cache XML content of AJAX response for PF
 * 
 * EXPERIMENTAL
 * 
 * This function is called delayed after main routine and prepare content of AJAX XML response.
 * The AJAX request should be performed from client browser immediatly after DOM ready.
 * This approach should parallel PF processing with page loading, but has obvious cons - $product_filter var is not available
 * in script for other purposes.
 */
function cw_product_filter_build_content($key, $data, $from_tbls, $query_joins, $where) {
    global $smarty,$config,$ajax_blocks;
    
    if (!cw_cache_get($key,'delayed_content')) {
        $a = microtime(true);
        $pfr = cw_call('cw_product_filter_build', array($data, $from_tbls, $query_joins, $where));
        $b = microtime(true);
        $smarty->assign('product_filter', $pfr);
        
        cw_load('ajax');
        cw_ajax_add_block(array(
            'id' => 'product_filter_content',
            'template' => 'customer/product-filter/menu-view/'.$config['product']['pf_template'].'.tpl'
        ));
        cw_ajax_add_block(array(
            'id' => 'product_filter_description',
            'content' => 'DESCRIPTION'
        ));
        $smarty->assign('ajax_blocks',$ajax_blocks);
        
        $content = cw_display('main/ajax/ajax_response.tpl',$smarty,false);
        
        cw_cache_save($content,$key,'delayed_content');
    }
    
    return true;
}
