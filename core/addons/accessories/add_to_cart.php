<?php
    $added_accessories = array();
    $product_accessories = array_merge((array)$product_accessories,(array)$product_recommended);
    if (is_array($product_accessories)) {
        foreach($product_accessories as $key=>$val) {
            if (empty($val['add'])) continue;
            $add_product = array();
            $pid = abs(intval($val['product_id']));
            $type = (!empty($val['type'])?$val['type']:'product_accessories').'_options';
            $options =& $$type;
            $add_product['product_id'] = $pid;
            $add_product['amount'] = abs(intval($val['amount']));
            $add_product['product_options'] = $options[$pid];
            $add_product['price'] = abs(doubleval($val['price']));
            if ($add_product['amount'] > 0) {
                $result = cw_call('cw_warehouse_add_to_cart_simple', array($pid,$add_product['amount'],$add_product['product_options'],$add_product['price']));
                if ($result['cartid']) {
                    $added_accessories[$pid] = $result;
                }
            }
        }

        if (!empty($added_accessories)) {
            foreach($added_accessories as $pid=>$v) {
                $added_accessories[$pid] = cw_func_call('cw_product_get',array('id' => $pid, 'user_account' => $user_account, 'info_type' => 0));
            }
            $smarty->assign('added_accessories', $added_accessories);
        }

    }
