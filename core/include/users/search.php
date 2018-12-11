<?php
cw_load('warehouse', 'map', 'image', 'category', 'doc');

$search_data = &cw_session_register('search_data', array());

$save_search_id = &cw_session_register('save_search_id', 0);

$current_loaded_search_id = &cw_session_register('current_loaded_search_id', 0);

if ($REQUEST_METHOD == 'GET' && $mode == 'search') {
    if ($new_search) $search_data['users'][$usertype] = array();
}

if ($action == 'reset') {
    $search_data['users'][$usertype] = array();
    cw_header_location("index.php?target=$target&mode=search");

} elseif ($action == 'save_search_load') {
    if (!empty($save_search_restore)) {
        $saved_search_data = cw_query_first("select * from $tables[saved_search] where ss_id='$save_search_restore' and type='C'");
        if (!empty($saved_search_data)) {
            if (!empty($saved_search_data['params'])) {
                $search_data['users'][$usertype] = unserialize($saved_search_data['params']);  
                $current_loaded_search_id = $save_search_restore;
                cw_add_top_message("Loaded '$saved_search_data[name]'", 'I');
            }   
        }
    } else {
        $current_loaded_search_id = 0;
        $save_search_id = 0;
        $search_data['users'][$usertype] = 0;
    }
    cw_header_location("index.php?target=$target&mode=search");
} elseif ($action == 'delete_search_load') {
    if ($current_loaded_search_id)  {
        db_query("delete from $tables[saved_search] where ss_id = '$current_loaded_search_id'"); 
        $current_loaded_search_id = 0;
        $save_search_id = 0;
        $search_data['users'][$usertype] = 0;
    }
    cw_header_location("index.php?target=$target&mode=search");
}
elseif ($REQUEST_METHOD == "POST") {

    $current_loaded_search_id = 0;

    $date_fields = array (
        'admin' =>array('creation_date_start' => 0, 'creation_date_end' => 1, 'modify_date_start' => 0, 'modify_date_end' => 1),
        'orders' => array('order_date_start' => 0, 'order_date_end' => 1),
        'marketing' => array('docs_start' => 0, 'docs_end' => 1),
        'saldo' => array('date_start' => 0, 'date_end' => 1),
    );

    $multiple_fields = array(
        'sale' => array('membership', 'sales_manager', 'language'),
        'mail' => array('news_list'),
    );

    cw_core_process_date_fields($posted_data, $date_fields, $multiple_fields);

    if (!empty($posted_data)) {
        $posted_data['orders']['orders_product'] = intval($orders_product); // for now it accepts only one ID
        $search_data['users'][$usertype] = $posted_data;
        // TODO: remove js_tab based on custom smarty block js_tabs
        $search_data['users'][$usertype]['js_tab'] = $js_tab;
    }
    $search_data['users'][$usertype]['search_sections'] = $search_sections;


//    if (!empty($search_data['users'][$usertype]['orders'])) {
//        cw_call('cw_doc_save_history_totals_by_customer', array(array()));
//    }

    if (!empty($search_data['users'][$usertype]['orders']['orders_count_from']) || 
        !empty($search_data['users'][$usertype]['orders']['orders_count_to']) || 
        !empty($search_data['users'][$usertype]['orders']['avg_subtotal_from']) || 
        !empty($search_data['users'][$usertype]['orders']['avg_subtotal_to']) || 
        !empty($search_data['users'][$usertype]['orders']['total_spent_from']) || 
        !empty($search_data['users'][$usertype]['orders']['total_spent_to'])) 
    {
        db_query("delete from $tables[customers_docs_stats_processed_docs]");
        db_query("delete from $tables[customers_docs_stats]");

        $valid_statuses = cw_query_column("select code from $tables[order_statuses] where inventory_decreasing=1");

        db_query("replace into $tables[customers_docs_stats] (customer_id, avg_subtotal, total_spent, orders_count) select $tables[docs_user_info].customer_id, avg($tables[docs_info].subtotal), sum($tables[docs_info].total), count(*) from $tables[docs_info], $tables[docs_user_info], $tables[docs] where $tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id and $tables[docs_info].doc_info_id = $tables[docs].doc_info_id and $tables[docs].status in ('".implode("','",$valid_statuses)."') group by $tables[docs_user_info].customer_id");
        db_query("replace into $tables[customers_docs_stats_processed_docs] (doc_id) select doc_id from $tables[docs]");
    }

    if (!empty($search_data['users'][$usertype]['orders']['category_ids'])) {
//init categories history - do not do this here, somewhere else: in order place func
//        cw_call('cw_doc_save_history_categories', array(array())); 
    }

    if (!empty($search_data['users'][$usertype]['orders']['attributes'])) {
//init attributes history - do not do this here, somewhere else: in order place func
//        cw_call('cw_doc_save_history_attributes', array(array()));
    }

    if ($action == 'save_search' && !empty($save_search_name)) {
        if ($save_search_restore) {
            cw_array2update('saved_search', array('name'=>addslashes($save_search_name), 'type'=>'C', 'sql_query'=>$sql_query4search, 'params'=>serialize($search_data['users'][$usertype])), "ss_id = '$save_search_restore'");
            $save_search_id = $save_search_restore;
            $current_loaded_search_id = $save_search_restore; 
        } else { 
            $save_search_id = cw_array2insert('saved_search', array('name'=>addslashes($save_search_name), 'type'=>'C', 'sql_query'=>$sql_query4search, 'params'=>serialize($search_data['users'][$usertype])));
        }
        cw_add_top_message("Saved search '$save_search_name'", 'I');      
    }

    cw_header_location("index.php?target=$target&mode=search#result");
}

if (empty($search_data['users'][$usertype])) {
    $search_data['users'][$usertype] = array(
        'basic_search' => array('by_username' => 1, 'by_firstname' => 1, 'by_lastname' => 1, 'by_email' => 1, 'by_customer_id' => 1, 'by_company' => 1),
        'search_sections' => array('basic_search' => 1),
        'address' => array('type' => 1),
    );
}

if (empty($search_data['users'][$usertype]['sort_field'])) {
    $search_data['users'][$usertype]['sort_field'] = "username";
    $search_data['users'][$usertype]['sort_direction'] = 0;
}

if (!empty($sort) && in_array($sort, array("username","name","email","usertype","last_login",'phone','zipcode'))) {
    $search_data['users'][$usertype]['sort_field'] = $sort;
    $search_data['users'][$usertype]['sort_direction'] = abs(intval((isset($sort_direction)?$sort_direction:$search_data['users'][$usertype]['sort_direction'])) - 1);
}

if (!empty($page) && $search_data['users'][$usertype]['page'] != intval($page)) {
    $search_data['users'][$usertype]['page'] = $page;
}

if ($mode == 'search') {
    $fields = array();
    $from_tbls = array();
    $query_joins = array();
    $where = array();
    $groupbys = array();
    $having = array();
    $orderbys = array();

    $data = $search_data['users'][$usertype];

    $fields[] = "$tables[customers].*";

    $from_tbls[] = 'customers_system_info';
    $from_tbls[] = "customers";
    $groupbys[] = "$tables[customers].customer_id";

    $where[] = "$tables[customers].usertype in ('".$usertype.($usertype == 'C'?"', 'R":'')."')";

    $fields[] = "$tables[customers_system_info].last_login";
    $where[] = "$tables[customers_system_info].customer_id=$tables[customers].customer_id";

    $fields[] = "ca.firstname";
    $fields[] = "ca.lastname";
    
    $fields[] = "ca.phone";
    $fields[] = "ca.zipcode";    

    $query_joins['ca'] = array(
        'tblname' => 'customers_addresses',
        'on' => "ca.customer_id = $tables[customers].customer_id and ca.main = 1",
    );
/*
    $query_joins['docs_user_info'] = array(
        'on' => "$tables[docs_user_info].customer_id = $tables[customers].customer_id",
    );
*/
    $to_join = array();

    if ($shop_company && in_array($usertype, array('C', 'R'))) {
    	$to_join['customers_customer_info'] = true;
        $where[] = "$tables[customers_customer_info].company_id='$shop_company'";
    }

    if ($data['basic_search']['substring']) {
        $data['basic_search']['substring'] = trim($data['basic_search']['substring']);
        $condition = array();

        if (!empty($data['basic_search']['by_firstname'])) {
            $condition[] = "$tables[customers_addresses].firstname LIKE '%".$data['basic_search']['substring']."%'";
            $to_join['customers_addresses'] = true;
        }

        if (!empty($data['basic_search']['by_lastname'])) {
            $condition[] = "$tables[customers_addresses].lastname LIKE '%".$data['basic_search']['substring']."%'";
            $to_join['customers_addresses'] = true;
        }

        if (preg_match("/^(.+)(\s+)(.+)$/", $data['basic_search']['substring'], $found) && !empty($data['basic_search']['by_firstname']) && !empty($data['basic_search']['by_lastname']))
            $condition[] = "$tables[customers_addresses].firstname LIKE '%".$found[1]."%' AND $tables[customers_addresses].lastname LIKE '%".$found[3]."%'";

        if ($data['basic_search']['by_email'])
            $condition[] = "$tables[customers].email LIKE '%".$data['basic_search']['substring']."%'";

        if ($data['basic_search']['by_customer_id'])
            $condition[] = "$tables[customers].customer_id = '".$data['basic_search']['substring']."'";

        if ($data['basic_search']['by_company']) {
            $condition[] = "$tables[customers_customer_info].company LIKE '%".$data['basic_search']['substring']."%'";
            $to_join['customers_customer_info'] = true;
        }


        $register_fields_text = cw_call('cw_user_search_get_register_fields', array($usertype, 'T'));
        foreach ($register_fields_text as $register_field_id=>$register_field) {
            if ($data['basic_search']['by_register_field_'.$register_field_id]) {
                $tbl_alias = 'rfv_'.$register_field_id;
                $query_joins[$tbl_alias] = array(
                    'tblname' => 'register_fields_values',
                    'on' => "$tbl_alias.customer_id = $tables[customers].customer_id and $tbl_alias.field_id='$register_field_id'",
                );     
                $condition[] = "$tbl_alias.value LIKE '%".$data['basic_search']['substring']."%'";
            }
        }

        if ($condition)
            $where[] = "(".implode(" OR ", $condition).")";
    }

    if ($data['search_sections']['adv_search_address']) {
        $to_join['customers_addresses'] = true;

        if (!empty($data['address']['city']))
            $where[] = "$tables[customers_addresses].city like '%".$data['address']['city']."%'";

        if (!empty($data['address']['state'])){
         if(is_array($data['address']['state']))
            $where[] = "$tables[customers_addresses].state IN ('".implode("','", $data['address']['state'])."') ";
          else
            $where[] = "$tables[customers_addresses].state='".$data['address']['state']."'";
        }

        if (!empty($data['address']['country']))
            $where[] = "$tables[customers_addresses].country='".$data['address']['country']."'";

        if (!empty($data['address']['zipcode']))
            $where[] = "$tables[customers_addresses].zipcode LIKE '%".$data['address']['zipcode']."%'";

        if (!empty($data['address']['phone']))
            $where[] = "$tables[customers_addresses].zipcode LIKE '%".$data['address']['phone']."%'";

        if ($data['address']['type'] == '3')
            $where[] = "($tables[customers_addresses].main = 1 or $tables[customers_addresses].current = 1)";
        elseif($data['address']['type'] == '1')
            $where[] = "($tables[customers_addresses].main = 1)";
        elseif($data['address']['type'] == '2')
            $where[] = "($tables[customers_addresses].current = 1)";

    }

    $admin_condition = array();
    if ($data['search_sections']['adv_search_admin']) {

        if (!empty($data['admin']['by_create_date'])) {
            if ($data['admin']['creation_date_start'])
                $admin_condition[] = "$tables[customers_system_info].creation_date >= '".$data['admin']['creation_date_start']."'";

            if ($data['admin']['creation_date_end'])
                $admin_condition[] = "$tables[customers_system_info].creation_date <= '".$data['admin']['creation_date_end']."'";
        }

        if (!empty($data['admin']['by_modify_date'])) {
            if ($data['admin']['modify_date_start'])
                $admin_condition[] = "$tables[customers_system_info].modification_date >= '".$data['admin']['modify_date_start']."'";

            if ($data['admin']['modify_date_end'])
                $admin_condition[] = "$tables[customers_system_info].modification_date <= '".$data['admin']['modify_date_end']."'";
        }

        if (count($data['admin']['membership']))
            $admin_condition[] = "$tables[customers].membership_id in ('".implode("', '", $data['admin']['membership'])."')";


        $register_fields_checkbox = cw_call('cw_user_search_get_register_fields', array($usertype, 'C'));
        foreach ($register_fields_checkbox as $register_field_id=>$register_field) {
            if ($data['admin']['by_register_field_'.$register_field_id]) {
                $tbl_alias = 'rfv_'.$register_field_id;
                $query_joins[$tbl_alias] = array(
                    'tblname' => 'register_fields_values',
                    'on' => "$tbl_alias.customer_id = $tables[customers].customer_id and $tbl_alias.field_id='$register_field_id'",
                );
                $admin_condition[] = "($tbl_alias.value='Y')";
            }
        }

        if (!empty($admin_condition))
            $where[] = implode(" AND ", $admin_condition);
    }

# orders
    if ($data['search_sections']['adv_search_orders']) {

        if (!empty($data['orders']['order_date_start']) || !empty($data['orders']['order_date_end'])
            || !empty($data['orders']['order_from']) || !empty($data['orders']['order_to'])) {
            $query_joins['docs_user_info'] = array(
                "on" => "$tables[docs_user_info].customer_id = $tables[customers].customer_id",
                'parent' => 'customers',
                'is_inner' => 1,
            );
            $query_joins['docs'] = array(
                "on" => "$tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id".
                    ($data['orders']['order_date_start']?" and $tables[docs].date >= '".$data['orders']['order_date_start']."'":"").
                    ($data['orders']['order_date_end']?" and $tables[docs].date <= '".$data['orders']['order_date_end']."'":"").
                    ($data['orders']['order_from']?" and $tables[docs].doc_id >= '".$data['orders']['order_from']."'":"").
                    ($data['orders']['order_to']?" and $tables[docs].doc_id <= '".$data['orders']['order_to']."'":""),
                'parent' => 'docs_user_info',
                'is_inner' => 1,
            );
        }

        if (!empty($data['orders']['orders_product']) || !empty($data['orders']['product_price_from']) || !empty($data['orders']['product_price_to'])) {
            $query_joins['docs_user_info'] = array(
                "on" => "$tables[docs_user_info].customer_id = $tables[customers].customer_id",
                'parent' => 'customers',
                'is_inner' => 1,
            );

            if (empty($query_joins['docs']))
                $query_joins['docs'] = array(
                    "on" => "$tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id",
                    'parent' => 'docs_user_info',
                    'is_inner' => 1,
                );

            $docs_items_cond = array();
            if (!empty($data['orders']['orders_product'])) {
                $docs_items_cond[] = "$tables[docs_items].product_id = '".$data['orders']['orders_product']."'";
            }  
            if (!empty($data['orders']['product_price_from'])) {
                $docs_items_cond[] = "$tables[docs_items].price >= '".$data['orders']['product_price_from']."'";
            }  
            if (!empty($data['orders']['product_price_to'])) {
                $docs_items_cond[] = "$tables[docs_items].price <= '".$data['orders']['product_price_to']."'";
            }

            $query_joins['docs_items'] = array(
                "on" => "$tables[docs_items].doc_id = $tables[docs].doc_id".
                " and ".implode(' and ', $docs_items_cond),
                'parent' => 'docs',
                'is_inner' => 1,
            );
        }

        if (!empty($data['orders']['orders_count_from']) || !empty($data['orders']['orders_count_to']) || !empty($data['orders']['avg_subtotal_from']) || !empty($data['orders']['avg_subtotal_to']) || !empty($data['orders']['total_spent_from']) || !empty($data['orders']['total_spent_to'])) {
            $query_joins['customers_docs_stats'] = array(
                "on" => "$tables[customers_docs_stats].customer_id = $tables[customers].customer_id".
                 ($data['orders']['orders_count_from']?" and $tables[customers_docs_stats].orders_count >= '".$data['orders']['orders_count_from']."'":"").
                 ($data['orders']['orders_count_to']?" and $tables[customers_docs_stats].orders_count <= '".$data['orders']['orders_count_to']."'":"").
                 ($data['orders']['avg_subtotal_from']?" and $tables[customers_docs_stats].avg_subtotal >= '".$data['orders']['avg_subtotal_from']."'":"").
                 ($data['orders']['avg_subtotal_to']?" and $tables[customers_docs_stats].avg_subtotal <= '".$data['orders']['avg_subtotal_to']."'":"").
                 ($data['orders']['total_spent_from']?" and $tables[customers_docs_stats].total_spent >= '".$data['orders']['total_spent_from']."'":"").
                 ($data['orders']['total_spent_to']?" and $tables[customers_docs_stats].total_spent <= '".$data['orders']['total_spent_to']."'":""),
                'parent' => 'customers',
                'is_inner' => 1,
            );
        }


        if (!empty($data['orders']['category_ids'])) {
            $categories_conditions = array();
            foreach ($data['orders']['category_ids'] as $search_categ) {
                if (empty($search_categ)) continue;
                $all_subcats = cw_category_get_subcategories($search_categ);
                $subcat_ids[$search_categ] = 1; 
                foreach($all_subcats as $subcat) {
                    $subcat_ids[$subcat['category_id']] = 1; 
                }
                $categories_conditions[] = "$tables[doc_history_categories].category_id in ('".implode("','",array_keys($subcat_ids))."')";
            }  

            if (!empty($categories_conditions)) {

                $query_joins['docs_user_info'] = array(
                    "on" => "$tables[docs_user_info].customer_id = $tables[customers].customer_id",
                    'parent' => 'customers',
                    'is_inner' => 1,
                );

                if (empty($query_joins['docs']))
                    $query_joins['docs'] = array(
                        "on" => "$tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id",
                        'parent' => 'docs_user_info',
                        'is_inner' => 1,
                    );

                $query_joins['doc_history_categories'] = array(
                    "on" => "$tables[doc_history_categories].doc_id = $tables[docs].doc_id".
                    " and (".implode(" or ", $categories_conditions).")",
                    'parent' => 'docs',
                    'is_inner' => 1,
                );
            }
 
        }

        if (!empty($data['orders']['attributes'])) {
            $attributes_conditions = array();
            $operations_codes = array('eq'=>'=', 'lt'=>'<', 'le'=>'<=', 'gt'=>'>', 'ge'=>'>=');
            foreach ($data['orders']['attributes'] as $search_attr) { 
                foreach ($search_attr['value'] as $sa_value) {
                    $sa_value_string = cw_query_first_cell("select value from $tables[attributes_default] where attribute_id='$search_attr[attribute_id]' and attribute_value_id='$sa_value'");
                    if (in_array($search_attr['operation'], array_keys($operations_codes))) {
                        $attributes_conditions[] = "($tables[doc_history_attributes].attribute_id='$search_attr[attribute_id]'". 
                           " and $tables[doc_history_attributes].value".$operations_codes[$search_attr['operation']]."'$sa_value_string')"; 
                    } elseif ($search_attr['operation'] == 'bt') {
                        $min_max = explode(",",$sa_value);  
                        if (!empty($min_max[0]) && !empty($min_max[1])) {
                            $attributes_conditions[] = "($tables[doc_history_attributes].attribute_id='$search_attr[attribute_id]'".
                           " and $tables[doc_history_attributes].value between '$min_max[0]' and '$min_max[1]')";
                        }
                    } elseif ($search_attr['operation'] == 'in') {
                        $vals_set = explode(",", $sa_value); 
                        if (!empty($vals_set))   
                            $attributes_conditions[] = "($tables[doc_history_attributes].attribute_id='$search_attr[attribute_id]'".
                            " and $tables[doc_history_attributes].value in ('".implode("','", $vals_set)."'))";                         
                    } 
                }  
            }
            if (!empty($attributes_conditions)) {

                $query_joins['docs_user_info'] = array(
                    "on" => "$tables[docs_user_info].customer_id = $tables[customers].customer_id",
                    'parent' => 'customers',
                    'is_inner' => 1,
                );
  
                if (empty($query_joins['docs']))
                    $query_joins['docs'] = array(
                        "on" => "$tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id",
                        'parent' => 'docs_user_info',
                        'is_inner' => 1,
                    );
  
                $query_joins['doc_history_attributes'] = array(
                    "on" => "$tables[doc_history_attributes].doc_id = $tables[docs].doc_id".
                    " and ( ".implode(" or ", $attributes_conditions)." )",  
                    'parent' => 'docs',
                    'is_inner' => 1,
                );
            }
        }
    }

// TODO: Find where search customers by saldo is used. Delete if nowhere.
    if ($data['search_sections']['adv_search_saldo']) {
        if ($data['saldo']['by_major'] || $data['saldo']['by_minor'] || $data['saldo']['by_zero']) {
            $query_joins['docs_user_info'] = array(
                "on" => "$tables[docs_user_info].customer_id = $tables[customers].customer_id",
                'parent' => 'customers',
            );
            $query_joins['docs'] = array(
                "on" => "$tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id".($data['saldo']['date_start']?" and $tables[docs].date >= '".$data['saldo']['date_start']."'":"").($data['saldo']['date_end']?" and $tables[docs].date <= '".$data['saldo']['date_end']."'":""),
                'parent' => 'docs_user_info',
            );
            $query_joins['docs_info'] = array(
                "on" => "$tables[docs_info].doc_info_id = $tables[docs_user_info].doc_info_id",
                'parent' => 'docs',
            );
        }

        $saldo_cond = array();
        if ($data['saldo']['by_major'])
            $saldo_cond[] = "sum($tables[docs_info].total) >= '".$data['saldo']['major']."'";

        if ($data['saldo']['by_minor'])
            $saldo_cond[] = "sum($tables[docs_info].total) <= '".$data['saldo']['minor']."'";

        if ($data['saldo']['by_zero'])
            $saldo_cond[] = "sum($tables[docs_info].total) = 0";

        if ($saldo_cond)
            $having[] = "(".implode(' OR ', $saldo_cond).")";
    }

/*
	if (empty($query_joins['docs_user_info'])) {
		$query_joins['docs_user_info'] = array(
			"on" 	 => "$tables[docs_user_info].customer_id = $tables[customers].customer_id",
			'parent' => 'customers'
		);
	}

	if (empty($query_joins['docs'])) {
		$query_joins['docs'] = array(
			"on" 	 => "$tables[docs].created_by = $tables[customers].customer_id and $tables[docs].type='O'",
			'parent' => 'customers',
            'pos'   => 5
		);
	}

	if (empty($query_joins['docs_items'])) {
		$query_joins['docs_items'] = array(
			"on" => "$tables[docs_items].doc_id = $tables[docs].doc_id",
			'parent' => 'docs'
		);
	}
*/

    if ($to_join['customers_addresses']) {
        $query_joins['customers_addresses'] = array(
            'on' => "$tables[customers_addresses].customer_id = $tables[customers].customer_id",
        );
    }

// TOFIX: refresh page F5 switches order direction while URL still the same
    if ($data['sort_field']) {
        $direction = ($data['sort_direction'] ? "DESC" : "ASC");
        switch ($data['sort_field']) {
            case 'name':
                $orderbys[] = "$tables[customers_customer_info].company $direction, ca.lastname $direction, ca.firstname $direction";
                $to_join['customers_customer_info'] = true;
                break;
            case "last_login":
                $orderbys[] = "$tables[customers_system_info].last_login $direction, customer_id";
                break;
          case 'orders':
                $orderbys[] = "orders $direction";
                break;
          case 'phone':
                $orderbys[] = "phone $direction";
                break;
          case 'zipcode':
                $orderbys[] = "zipcode $direction";
                break;
            case "usertype":
            case "email":
                $orderbys[] = "$tables[customers].".$data['sort_field']." $direction";
        }
    }

    if ($to_join['customers_customer_info']) {
	    $query_joins['customers_customer_info'] = array(
	        'on' => "$tables[customers_customer_info].customer_id = $tables[customers].customer_id",
	    );
    }

    cw_event('on_prepare_search_users', $s = array($data, &$fields, &$from_tbls, &$query_joins, &$where, &$groupbys, &$having, &$orderbys));
//print("<pre>"); print_r($s); print("</pre>");
    $count_query = cw_db_generate_query(array('count(*)'), $from_tbls, $query_joins, $where, $groupbys, $having, array());
//print($count_query);
    $_res = db_query($count_query);
    $total_items = db_num_rows($_res);
    db_free_result($_res);

    $navigation = cw_call('cw_core_get_navigation', array($target,$total_items,$page));
    $navigation['script'] = "index.php?target=$target&mode=search";
    $smarty->assign('navigation', $navigation);

    if ($total_items) {
        $page = $data['page'];

        $user_search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
        $users = cw_query($q="$user_search_query LIMIT $navigation[first_page], $navigation[objects_per_page]");

        if ($action == 'export_emails') {
            $export_columns_names = array('firstname'=>'Firstname','lastname'=>'Lastname','email'=>'Email');
            $delimiter = $config['General']['user_emails_export_delimiter'];

            header ("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=customers_emails.csv");

            print(implode($delimiter,$export_columns_names)."\n"); 
            $u_keys = array();
            $result = mysql_query($user_search_query);
            while ($row = mysql_fetch_assoc($result)) {
                $exp_line = array();

                $row['email'] = strtolower($row['email']);

                foreach ($export_columns_names as $colname=>$coltitle)  
                    $exp_line[$colname] = $row[$colname];
                
                
                $u_key = md5($row['email']);
                if (!isset($u_keys[$u_key])) {
                    $u_keys[$u_key] = 1;
                    print(implode($delimiter,$exp_line)."\n");
                }   
            } 
            exit;
        }

        if ($save_search_id > 0) {
            cw_array2update('saved_search', array("sql_query"=>addslashes($user_search_query)), "ss_id = '$save_search_id'");
            $save_search_id = 0;
        } 

        $valid_statuses = cw_query_column("select code from $tables[order_statuses] where inventory_decreasing=1");

        foreach ($users as $k=>$v) {
            $users[$k]['orders'] = cw_query_first_cell("SELECT count(d.doc_id) FROM $tables[docs_user_info] dui, $tables[docs] d WHERE dui.customer_id=$v[customer_id] AND dui.doc_info_id=d.doc_info_id AND d.type='O' and d.status in ('".implode("','",$valid_statuses)."')");
        }
        $smarty->assign('users', $users);
    }
    $smarty->assign('mode', $mode);
}

$predefined_lng_variables[] = 'lbl_search_user_'.$usertype;

if(is_array($search_data['users'][$usertype]['address']['state']))
   $search_data['users'][$usertype]['address']['state'] = json_encode($search_data['users'][$usertype]['address']['state']);

$smarty->assign('js_tab', $search_data['users'][$usertype]['js_tab']);
$smarty->assign('mode', $mode);
$smarty->assign('payment_methods', cw_func_call('cw_payment_search', array('data' => array('type' => 1, 'active' => 1))));
$smarty->assign('memberships', cw_user_get_memberships($usertype == 'C'?array('C', 'R') : $usertype ));
$smarty->assign('employees', cw_user_get_short_list('E'));
$smarty->assign('sales_managers', cw_user_get_short_list('B'));

$smarty->assign('countries', cw_map_get_countries());
$smarty->assign('states', cw_map_get_states());

$smarty->assign('search_prefilled', $search_data['users'][$usertype]);
$smarty->assign('current_search_type', $usertype);

$smarty->assign('saved_user_search', cw_query("select name, ss_id from $tables[saved_search] where type='C' order by name, ss_id"));
$smarty->assign('current_loaded_search_id', $current_loaded_search_id);
$smarty->assign('current_loaded_search_name', cw_query_first_cell("select name from $tables[saved_search] where type='C' and ss_id='$current_loaded_search_id'"));

if ($is_ajax) {
    global $ajax_blocks;
    $ajax_blocks[] = array(
        'id' => 'search_result',
        'template' => 'admin/users/search_results.tpl',
    );
    $ajax_blocks[] = array(
        'id' => 'script',
        'template' => 'js/navigation.js',
    );
}
