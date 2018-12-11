<?php

function cw_dod_generate() {

    global $tables, $config, $current_language;

    $generator = cw_query_first("select * from $tables[dod_generators] where active=1 and startdate<='".time()."' and enddate>'".time()."'  and dod_interval<>0 order by position asc, generator_id asc");

    if (!empty($generator)) {
        //check last generation date
        $last_gen_date = $generator['current_offer_date'];        
        $hrs_since_last_generation = intval((time()-$last_gen_date)/3600);
        $generate_again = false;
        if ($generator['dod_interval_type'] == 'D') {
            $generate_again = ($hrs_since_last_generation >= $generator['dod_interval']*24);
            $offer_enddate = min($generator['enddate'], time()+$generator['dod_interval']*24*3600);
        } elseif ($generator['dod_interval_type'] == 'T')  {
            $dod_period_hrs = intval(($generator['enddate'] - $generator['startdate'])/3600);
            $hrs_interval = intval($dod_period_hrs/$generator['dod_interval']);
            $generate_again = ($hrs_since_last_generation >= $hrs_interval);
            $offer_enddate = min($generator['enddate'], time()+($dod_period_hrs/$generator['dod_interval'])*3600);
        } 

        if ($generate_again || $_GET['force_generate']) {
            if (!empty($generator['used_pids'])) {
                $used_pids = explode(';', $generator['used_pids']);  
            } else {
                $used_pids = array(); 
            } 

            $dod_products = cw_query_column("select dbd.object_id from $tables[dod_bonus_details] dbd inner join $tables[dod_bonuses] db on db.generator_id=dbd.generator_id and db.bonus_id=dbd.bonus_id and db.type='".DOD_DISCOUNT."' where dbd.generator_id='$generator[generator_id]' and dbd.object_type='".DOD_OBJ_TYPE_PRODS."'");
            $dod_categories = cw_query_column("select dbd.object_id from $tables[dod_bonus_details] dbd inner join $tables[dod_bonuses] db on db.generator_id=dbd.generator_id and db.bonus_id=dbd.bonus_id and db.type='".DOD_DISCOUNT."' where dbd.generator_id='$generator[generator_id]' and dbd.object_type='".DOD_OBJ_TYPE_CATS."'"); 
            $dod_manufacturers = cw_query_column("select dbd.object_id from $tables[dod_bonus_details] dbd inner join $tables[dod_bonuses] db on db.generator_id=dbd.generator_id and db.bonus_id=dbd.bonus_id and db.type='".DOD_DISCOUNT."' where dbd.generator_id='$generator[generator_id]' and dbd.object_type='".DOD_OBJ_TYPE_MANS."'");
            $dod_attributes = cw_query("select dbd.*  from $tables[dod_bonus_details] dbd inner join $tables[dod_bonuses] db on db.generator_id=dbd.generator_id and db.bonus_id=dbd.bonus_id and db.type='".DOD_DISCOUNT."' where dbd.generator_id='$generator[generator_id]' and dbd.object_type='".DOD_OBJ_TYPE_ATTR."'");
           

            //select products by dod conditions
            $data = array(); 
            $dod_data_where_pids = '';

            if ($dod_products) {
                $dod_data_where_pids = "$tables[products].product_id in ('".implode("','", $dod_products)."')";
            }    
            if ($dod_categories) {
                $data['search_in_subcategories'] = 1;
                $data['category_ids'] = $dod_categories;
            }    
 
            if ($dod_manufacturers) {
                $manufacturer_id_attribute = cw_query_first_cell("select attribute_id from $tables[attributes] where field='manufacturer_id' and addon='manufacturers'");
                if ($manufacturer_id_attribute) {
                    if (!isset($ret_params))   
                        $ret_params = array();

                    if (!isset($ret_params['query_joins'])) 
                        $ret_params['query_joins'] = array();

                    $ret_params['query_joins']['atv_manufacturer'] = array(
                        'tblname' => 'attributes_values',
                        'on' => "$tables[products].product_id=atv_manufacturer.item_id and atv_manufacturer.item_type='P' and atv_manufacturer.attribute_id = '$manufacturer_id_attribute' and atv_manufacturer.code in ('$current_language', '') and atv_manufacturer.value in ('".implode("','", $dod_manufacturers)."')",
                         'is_inner' => 1
                    );
                }
            } 

            if ($dod_attributes) {

                $param2_sql = array('eq'=>'=', 'lt'=>'<', 'le'=>'<=', 'gt'=>'>', 'ge'=>'=>');

                foreach ($dod_attributes as $attr_data_k => $attr_data) {
                    $is_def_values = cw_query_first("select * from $tables[attributes_default] where attribute_value_id='$attr_data[param1]' and attribute_id='$attr_data[object_id]'"); 
//print_r($is_def_values);print("<br><br>");
                    $sql_operation = $param2_sql[$attr_data['param2']];
                    if (empty($sql_operation)) continue;

                    if (!isset($ret_params)) 
                        $ret_params = array();

                    if (!isset($ret_params['query_joins'])) 
                        $ret_params['query_joins'] = array();

                    if ($is_def_values) {
                        $ret_params['query_joins']['atv_dod_'.$attr_data_k] = array(
                            'tblname' => 'attributes_values',
                            'on' => "$tables[products].product_id=atv_dod_$attr_data_k.item_id and atv_dod_$attr_data_k.item_type='P' and atv_dod_$attr_data_k.attribute_id = '$attr_data[object_id]' and atv_dod_$attr_data_k.code in ('$current_language', '')",
                            'is_inner' => 1
                        ); 
                        $ret_params['query_joins']['atd_dod_'.$attr_data_k] = array(
                            'tblname' => 'attributes_default',
                            'on' => "atd_dod_$attr_data_k.attribute_value_id=atv_dod_$attr_data_k.value and atv_dod_$attr_data_k.attribute_id=atd_dod_$attr_data_k.attribute_id and atd_dod_$attr_data_k.value$sql_operation'".addslashes($is_def_values['value'])."'",
                            'is_inner' => 1
                        );

                    } else {    
                        $ret_params['query_joins']['atv_dod_'.$attr_data_k] = array(
                            'tblname' => 'attributes_values',
                            'on' => "$tables[products].product_id=atv_dod_$attr_data_k.item_id and atv_dod_$attr_data_k.item_type='P' and atv_dod_$attr_data_k.attribute_id = '$attr_data[object_id]' and atv_dod_$attr_data_k.code in ('$current_language', '') and atv_dod_$attr_data_k.value$sql_operation'$attr_data[param1]'",
                            'is_inner' => 1
                        );
                    }  
                }
            }     

            global $user_account, $current_area, $items_per_page_targets, $target;
            $items_per_page_targets[$target] = 1;

            $new_pid = 0;
            $safety_cnt = 1000;
            while (!$new_pid && $safety_cnt > 0) {
                if (!empty($data) || !empty($dod_data_where_pids)) {

                    $data['sort_field'] = 'rand'; 
                    $data['flat_search'] = 1;
                    $dod_data_where = array();
                    if (!empty($dod_data_where_pids)) {
                        $dod_data_where[] = $dod_data_where_pids;   
                    }
                    if (!empty($used_pids)) {
                        $dod_data_where[] = "$tables[products].product_id not in ('".implode("','", $used_pids)."')";
                    }
                    $data['where'] = implode(' and ', $dod_data_where); 
                    list($products, $nav, $product_filter) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => 8, 'product_id_only'=>1), $ret_params);
                }
              
                $product = reset($products);
//print_r(array('product'=>$product));print("<br><br>");
                $new_pid = $product['product_id'];
                if (!$new_pid) {
                    if ($generator['no_item_repeat']) {
                        break;  
                    } else {
                        if (!empty($used_pids)) { 
                            array_shift($used_pids);
                        } else {
                            break;
                        } 
                    }
                }
                $safety_cnt--; 
            }
//die;
            if ($new_pid)   
                $used_pids[] = $new_pid;

            $generator['used_pids'] = implode(';', $used_pids);

            $regenerate_offer = true;

            if ($regenerate_offer) {
                //regenerate offer
                if (!empty($generator['current_offer_id'])) {
                    $offer_ids = array($generator['current_offer_id']);
                    $offer_ids_query = implode("', '", $offer_ids);

                    db_query("DELETE FROM $tables[ps_offers] WHERE offer_id IN ('" . $offer_ids_query . "')");
                    db_query("DELETE FROM $tables[ps_bonuses] WHERE offer_id IN ('" . $offer_ids_query . "')");
                    db_query("DELETE FROM $tables[ps_bonus_details] WHERE offer_id IN ('" . $offer_ids_query . "')");
                    db_query("DELETE FROM $tables[ps_conditions] WHERE offer_id IN ('" . $offer_ids_query . "')");
                    db_query("DELETE FROM $tables[ps_cond_details] WHERE offer_id IN ('" . $offer_ids_query . "')");

                    db_query("DELETE FROM $tables[attributes_values] WHERE item_id IN ('" . $offer_ids_query . "') and item_type='PS'"); 

                    foreach ($offer_ids as $offer_id) {
                        cw_image_delete($offer_id, PS_IMG_TYPE);
                    }
                    cw_attributes_cleanup($offer_ids, PS_ATTR_ITEM_TYPE);
                    cw_cache_clean('shipping_rates');
                }   

                if ($new_pid) {

                    cw_log_add('dod_generator', array('new DOD product selected'=>$new_pid)); 
                    $new_offer_id = cw_array2insert('ps_offers', 
                        array('title'=>'Deal Of The Day', 'description'=>$generator['description'], 'startdate'=>time(), 'enddate'=>$offer_enddate, 'active'=>1, )
                    );  
                }

                $current_offer_id = 0;

                if ($new_offer_id) {           
                    $mdm_attribute_id = cw_query_first_cell("select attribute_id from $tables[attributes] where addon='multi_domains' and item_type='PS'"); 
                    if ($mdm_attribute_id) {
                        cw_array2insert('attributes_values', array('item_id'=>$new_offer_id, 'attribute_id'=>$mdm_attribute_id, 'value'=>0, 'code'=>'', 'item_type'=>'PS'));
                    }  

                    //copy bonus and bonus details
                    $dod_bonuses = cw_query("select * from $tables[dod_bonuses] where generator_id='$generator[generator_id]' and unused=0");
                    foreach ($dod_bonuses as $dod_bonus) {
                        $_dod_bonus = $dod_bonus;
                        unset($_dod_bonus['generator_id']);
                        $_dod_bonus['offer_id'] = $new_offer_id;  
                        $new_bonus_id = cw_array2insert('ps_bonuses', $_dod_bonus);   
                        if ($_dod_bonus['type'] == 'D' && $_dod_bonus['apply'] == 3) {
                            cw_array2insert('ps_bonus_details', array('bonus_id'=>$new_bonus_id, 'offer_id'=>$new_offer_id, 'object_id'=>$new_pid, 'quantity'=>1, 'object_type'=>DOD_OBJ_TYPE_PRODS));  
                        } else {
                            $dod_bonus_details = cw_query("select * from $tables[dod_bonus_details] where generator_id='$generator[generator_id]' and bonus_id='$dod_bonus[bonus_id]'");
                            if (!empty($dod_bonus_details)) { 
                                foreach ($dod_bonus_details as $dod_bonus_detail) {
                                    $_dod_bonus_detail = $dod_bonus_detail;
                                    unset($_dod_bonus_detail['generator_id']);
                                    $_dod_bonus_detail['offer_id'] = $new_offer_id;
                                    $_dod_bonus_detail['bonus_id'] = $new_bonus_id;
                                    cw_array2insert('ps_bonus_details', $_dod_bonus_detail);   
                                }
                            }
                        }  
                    }
                    $new_cond_id = cw_array2insert('ps_conditions', array('type'=>'P', 'total'=>'0.00', 'offer_id'=>$new_offer_id));
                    if ($new_cond_id) {
                        cw_array2insert('ps_cond_details', array('cond_id'=>$new_cond_id, 'offer_id'=>$new_offer_id, 'object_id'=>$new_pid, 'quantity'=>1, 'object_type'=>DOD_OBJ_TYPE_PRODS));  
                    }  
                    $current_offer_id = $new_offer_id;  
                }
            }

            //update dod_generator fields

            cw_array2update('dod_generators', 
                array('current_offer_id'=>$current_offer_id, 
                      'used_pids'=>$generator['used_pids'], 
                      'current_offer_date'=>($current_offer_id?time():0)), 
                "generator_id='$generator[generator_id]'");

            if ($current_offer_id && !empty($config['deal_of_day']['dod_news_template']) && $config['deal_of_day']['dod_newslist']) {
                $newslist = cw_query_first("select * from $tables[newslists] where list_id='".$config['deal_of_day']['dod_newslist']."' and avail=1");  
                if (!empty($newslist)) {  
 
                    //create message 
                    global $smarty;

                    $smarty->assign('promotion', $generator);

                    $smarty->assign('product_id', $new_pid);
                    $product_info = cw_func_call('cw_product_get' ,array('id' => $new_pid, 'user_account' => $user_account, 'info_type' => 65535));
                    $smarty->assign('product', $product_info);
                    $smarty->assign('news_message', $config['deal_of_day']['dod_news_template']);
                    $message = cw_display("addons/deal_of_day/admin/generate_news.tpl", $smarty, false, $newslist['lngcode']);
         
                    $smarty->assign('news_message', $config['deal_of_day']['dod_news_template_subject']);
                    $message_subject = cw_display("addons/deal_of_day/admin/generate_news.tpl", $smarty, false, $newslist['lngcode']);

//                    $message = $smarty->display('addons/deal_of_day/admin/generate_news.tpl');    

                    print($message_subject."<hr />".$message);
                    if (!empty($message)) {
                        cw_array2insert('newsletter',
                            array('subject' => $message_subject,
                                  'body' => $message,
                                  'created_date' => time(),
                                  'send_date' => time(),
                                  'updated_date' => time(),
                                  'status' => 'N',
                                  'list_id' => $config['deal_of_day']['dod_newslist'],
                                  'show_as_news' => 1,
                                  'allow_html' => 1    
                            )   
                        );
                    } 
                }
            }

        }  
    }
    return $new_pid;
}
