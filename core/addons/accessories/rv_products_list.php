<?php
if ($config['accessories']['ac_rv_display_recently_viewed_pr'] == 'Y') {
    $recent_product_ids = cw_call('cw_ac_rv_product_get_cookie');
    if ($recent_product_ids) {
        $recent_product_ids = array_reverse($recent_product_ids);
        
        $data['where'] = "$tables[products].product_id in ('" . implode("', '", $recent_product_ids) . "')";
        list($result, $navigation) = cw_func_call('cw_product_search', array(
            'data' => $data,
            'user_account' => $user_account,
            'current_area' => $current_area,
            'info_type' => 65535
        ));
        
        $rv_products = array();
        foreach ($recent_product_ids as $id) {
            foreach ($result as $rv_pr) {
                if ($id == $rv_pr['product_id'])
                    array_push($rv_products, $rv_pr);
            }
            
        }
        $smarty->assign('rv_products', $rv_products);
    }
}
