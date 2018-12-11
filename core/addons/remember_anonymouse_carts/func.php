<?php
function cw_rc_cookie_process()
{
    global $smarty;

    if (!cw_rc_get_cookie('start'))
    {
        if ($arr_cookie = cw_rc_get_cookie('history'))
        {
            cw_session_start($arr_cookie[0]);         
            if (!cw_rc_set_cookie('history_temp', array($arr_cookie[0],$arr_cookie[1]))) 
                cw_rc_del_cookie('history_temp');
            
            if ($arr_seccion = cw_rc_get_session())
            {
                list($id, $date, $cost, $count) = $arr_seccion;
                
                if (intval($count)) cw_rc_set_cookie('history', array($arr_seccion[0], $arr_seccion[1]));
                else 
                {
                    cw_rc_del_cookie('history');
                    cw_rc_del_cookie('history_temp');
                }
                
                $pref = ''; $postf = '';
                if ($count_products = cw_rc_get_count_new_product($arr_cookie[1]))
                {
                    $lng_count = cw_get_langvar_by_name('products_added_last_entry_msg', false, false,true);
                    if (!empty($lng_count))
                    {
                        $pref = "($count_products) $lng_count.";
                        $smarty->assign('count_products', $pref);
                    }
                }
                if ($arr_seccion[3]) 
                {
                    $lng_cart = cw_get_langvar_by_name('your_cart_count_items_msg', false, false,true);
                    if (!empty($lng_cart))
                        $postf = str_replace('{count}', $arr_seccion[3], $lng_cart);
                }
                if (!empty($pref) || !empty($postf))
                    cw_rc_echo($smarty, "{$pref} {$postf}");
            }
            else
            {
                cw_rc_del_cookie('history');
                cw_rc_del_cookie('history_temp');
            } 
        }
        else
        {
            if ($arr_seccion = cw_rc_get_session())
            {
                list($id, $date, $cost, $count) = $arr_seccion;
                if (intval($count)) cw_rc_set_cookie('history', array($arr_seccion[0], $arr_seccion[1]));               
            }
            else
            {
                cw_rc_del_cookie('history');
                cw_rc_del_cookie('history_temp');
            }            
        }
        cw_rc_set_cookie('start');
    }
    else 
    {
        if ($arr_cookie = cw_rc_get_cookie('history_temp'))
        {
            cw_session_start($arr_cookie[0]);
            
            if ($count_products = cw_rc_get_count_new_product($arr_cookie[1]))
            {
                $lng_count = cw_get_langvar_by_name('products_added_last_entry_msg', false, false,true);
                $smarty->assign('count_products', "($count_products) $lng_count");
            }
        }
            
        if ($arr_seccion = cw_rc_get_session())
        {
            list($id, $date, $total, $count) = $arr_seccion;
            if (intval($count)) cw_rc_set_cookie('history', array($id, $date));               
            $rcsave = &cw_session_register('rcsave');
            if ($rcsave > 0)
            {
                if (($total>0)&&($total != $rcsave))
                {
                    cw_rc_echo($smarty, cw_get_langvar_by_name('your_cart_saved_msg', false, false,true));
                    $rcsave = $total;
                }
            }
            elseif ($total > 0) 
            {
                $rcsave = $total;
                cw_rc_echo($smarty, cw_get_langvar_by_name('your_cart_saved_msg', false, false,true));
            }
            
            if (!$count)
            {
                cw_rc_del_cookie('history');
                cw_rc_del_cookie('history_temp');  
            }
        }
        else
        {
            cw_rc_del_cookie('history');
            cw_rc_del_cookie('history_temp');
        }  
    } 
}


function cw_rc_get_cookie($type = '') {
    if (empty($type)) return false;
    
    switch ($type) {
     case 'history':
         if (empty($_COOKIE[RC_COOKIE_HISTORY])) return false;
         $tmp = stripcslashes($_COOKIE[RC_COOKIE_HISTORY]);
         break;
     case 'history_temp':
         if (empty($_COOKIE[RC_COOKIE_HISTORY_TEMP])) return false;
         $tmp = stripcslashes($_COOKIE[RC_COOKIE_HISTORY_TEMP]);    
         break;
     case 'start':
         if (!empty($_COOKIE[RC_COOKIE_START])) return true; else return false;
         break;
     default:
         return false;
    }
    
    list($id, $date) = unserialize($tmp);
    return (!empty($id)&&!empty($date)) ? array($id,$date) : false;
}


function cw_rc_set_cookie($type = '', $arr_data = array()) {
    if (empty($type)) return false;
    
    switch ($type) {
     case 'history':
         list($id, $date) = $arr_data;
         if (empty($id)||empty($date)) return false;
         return cw_set_cookie(RC_COOKIE_HISTORY,  serialize(array($id,$date)), time()+(365*24*60*60), "/");
         break;
     case 'history_temp':
         list($id, $date) = $arr_data;
         if (empty($id)||empty($date)) return false;
         return cw_set_cookie(RC_COOKIE_HISTORY_TEMP,  serialize(array($id,$date)), 0, "/");   
         break;
     case 'start':
         return cw_set_cookie(RC_COOKIE_START, '1', 0, '/');
         break;
     default:
         return false;
    }
    
    return false;
}


function cw_rc_del_cookie($type = '') {
    if (empty($type)) return false;
    
    switch ($type) {
     case 'history':
         return cw_set_cookie(RC_COOKIE_HISTORY,  '', 0, "/");
         break;
     case 'history_temp':
         return cw_set_cookie(RC_COOKIE_HISTORY_TEMP,  '', 0, "/");
         break;
     case 'start':
         return cw_set_cookie(RC_COOKIE_START, '', 0, '/');
         break;
     default:
         return false;
    }
    
    return false;
}


function cw_rc_get_session() {
    global $APP_SESSION_VARS, $APP_SESS_ID;
    
    if (empty($APP_SESS_ID)||empty($APP_SESSION_VARS)) return false; 
    
    $arr = $APP_SESSION_VARS['cart']['products'];    
    $count = 0;
    if (!empty($arr))
        if (is_array($arr))
            if (count($arr))
                foreach ($arr as $v)
                    if (intval($v['cartid'])) ++$count;
    
    $total = isset($APP_SESSION_VARS['cart']['info']['total'])?$APP_SESSION_VARS['cart']['info']['total']:0;

    $date = cw_core_get_time();
    
    return array($APP_SESS_ID,$date,$total,$count);
}


function cw_rc_get_count_new_product($date)
{
    if (empty($date)) return false;
    global $tables, $config;
	if ($config['Appearance']['categories_in_products'] == '1') {
		$query ="SELECT count(distinct cw_products.product_id) FROM cw_products
		  INNER JOIN cw_attributes_values ON cw_products.product_id=$tables[attributes_values].item_id and cw_attributes_values.item_type = 'P' and cw_attributes_values.attribute_id='1' and cw_attributes_values.value in ('0', '9')
		  LEFT JOIN cw_products_warehouses_amount ON cw_products_warehouses_amount.product_id = cw_products.product_id and cw_products_warehouses_amount.warehouse_customer_id = 0 and cw_products_warehouses_amount.variant_id=0 STRAIGHT_JOIN cw_products_prices
		  LEFT JOIN cw_products_categories ON cw_products_categories.product_id = cw_products.product_id
		  INNER JOIN cw_products_system_info ON cw_products_system_info.product_id = cw_products.product_id
		  STRAIGHT_JOIN cw_categories_memberships
		  STRAIGHT_JOIN cw_products_memberships
		  INNER JOIN cw_categories ON cw_categories.category_id = cw_products_categories.category_id
		  WHERE cw_products.status = 1
		  AND cw_products_system_info.creation_date >= '".db_escape_string($date)."'
		  AND cw_categories_memberships.membership_id IN (0, '0') AND cw_products_memberships.membership_id IN (0, '0')
		  AND cw_categories.status = 1 AND cw_products.product_type != 10
		  AND cw_products_prices.product_id = cw_products.product_id and cw_products_prices.quantity=1
		  AND cw_products_prices.membership_id in (0, '') AND cw_categories_memberships.category_id = cw_products_categories.category_id
		  AND cw_products_memberships.product_id = cw_products.product_id";
	} else {
		$query ="SELECT count(distinct cw_products.product_id) FROM cw_products
		  INNER JOIN cw_attributes_values ON cw_products.product_id=$tables[attributes_values].item_id and cw_attributes_values.item_type = 'P' and cw_attributes_values.attribute_id='1' and cw_attributes_values.value in ('0', '9')
		  LEFT JOIN cw_products_warehouses_amount ON cw_products_warehouses_amount.product_id = cw_products.product_id and cw_products_warehouses_amount.warehouse_customer_id = 0 and cw_products_warehouses_amount.variant_id=0 STRAIGHT_JOIN cw_products_prices
		  INNER JOIN cw_products_system_info ON cw_products_system_info.product_id = cw_products.product_id
		  STRAIGHT_JOIN cw_categories_memberships
		  STRAIGHT_JOIN cw_products_memberships
		  WHERE cw_products.status = 1
		  AND cw_products_system_info.creation_date >= '".db_escape_string($date)."'
		  AND cw_categories_memberships.membership_id IN (0, '0') AND cw_products_memberships.membership_id IN (0, '0')
		  AND cw_products.product_type != 10
		  AND cw_products_prices.product_id = cw_products.product_id and cw_products_prices.quantity=1
		  AND cw_products_prices.membership_id in (0, '')
		  AND cw_products_memberships.product_id = cw_products.product_id";
	}
    $count = cw_query_first_cell($query);
    if ($count) return $count; 
   
    return false;
}


function cw_rc_get_format_time($time)
{
    return !empty($time) ? date('d.m.o G:i', $time) : false;
}


function cw_rc_echo($smarty, $str) {
    if (empty($str)) return false;
    $top_message['content'] = $str;
    $smarty->assign('top_message', $top_message);
    $top_message = '';
}
