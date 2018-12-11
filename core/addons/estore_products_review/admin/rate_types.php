<?php
$location[] = array(cw_get_langvar_by_name("lbl_edit_rate_types"), "");


if ($REQUEST_METHOD == "POST") {

	if (isset($new_name))
		$new_name = trim($new_name);

	if ($action == "add" && !empty($new_name)) {
	    #
   	    # Add a new rating type
   	    #
        cw_array2insert('products_reviews_rating_types', array('name'=>$new_name, 'is_product'=>(!empty($new_is_product) ? "1" : "0"), 'is_global'=>(!empty($new_is_global) ? "1" : "0"), 'active'=> (!empty($new_active) ? "1" : "0"), 'orderby'=>$new_orderby));

		$top_message['content'] = cw_get_langvar_by_name("msg_adm_rate_types_add");
	
	}
	elseif ($action == "delete" and !empty($posted_data)) {
	#
	# Delete selected rating types
	#
		if (is_array($posted_data) && is_array($sel_rate_types)) {
            db_query("delete from $tables[products_reviews_rating_types] where rate_type_id in ('".implode("','",$sel_rate_types)."')");
		    $top_message['content'] = cw_get_langvar_by_name("msg_adm_rate_types_del");
		}
	}
	elseif ($action == "update" && !empty($posted_data)) {
	#
	# Update rate types list
	#
		if (is_array($posted_data) && is_array($rate_types)) {
			$updated = false;
			foreach ($rate_types as $key=>$value) {
				foreach ($posted_data as $k=>$v) {
				
					$v['name'] = trim($v['name']);
					if (empty($v['name']))
						continue;
                    
                    cw_array2update('products_reviews_rating_types', 
                        array( 'name'=>$v['name'], 
                               'is_product' => (!empty($v['is_product']) ? "1" : "0"), 
                               'is_global'  => (!empty($v['is_global']) ? "1" : "0"), 
                               'active'     => (!empty($v['active']) ? "1" : "0"), 
                               'orderby'    => intval($v['orderby'])), 
                               "rate_type_id = '$v[rate_type_id]'"); 				
				}
			}

		    $top_message['content'] = cw_get_langvar_by_name("msg_adm_rate_types_upd");
		}
	}
	
	cw_header_location("index.php?target=rate_types");

}

$smarty->assign('main', "rate_types");
