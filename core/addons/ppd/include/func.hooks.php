<?php
function cw_ppd_doc_change_status_C($doc_data, $return) {
    global $tables, $config;

    if (empty($doc_data) || !is_array($doc_data)) {
        return $return;
    }

    $doc_data['doc_id'] = (int) $doc_data['doc_id'];

    $_download_exists = cw_query_first_cell('SELECT download_id FROM ' . $tables['ppd_downloads'] . ' WHERE order_id = \'' . $doc_data['doc_id'] . '\'');

    if (!empty($_download_exists) || empty($doc_data['products'])) {
        return $return;
    }

    $values_for_ins = array();
    $data = array();

    $data['order_id'] = $doc_data['doc_id'];
    $data['customer_id'] = $doc_data['userinfo']['customer_id'];
    $data['allowed_number'] = (int) $config['ppd']['ppd_loading_attempts'];
    $data['counter'] = 0;
    $data['expiration_date'] = cw_core_get_time() + (int) $config['ppd']['ppd_link_lifetime'] * 60 * 60;


    foreach ($doc_data['products'] as $product) {

        $files = cw_query('SELECT file_id FROM ' . $tables['ppd_files'] . ' WHERE product_id = \'' . $product['product_id'] . '\' AND active = 1 AND perms_owner >= 4 AND perms_all = 0 ORDER BY number');
        if (empty($files) || !is_array($files)) {
            continue;
        }

        $data['product_id'] = $product['product_id'];

        foreach ($files as $file) {
            $data['file_id'] = $file['file_id'];
            $_replace_data = cw_query_first_cell('SELECT download_id FROM ' . $tables['ppd_downloads'] . ' WHERE product_id = \'' . $data['product_id'] . '\' AND file_id = \'' . $data['file_id'] . '\'');
            if (!empty($_replace_data)) {
                $data['download_id'] = $_replace_data;
                $query = 'REPLACE INTO ' . $tables['ppd_downloads'] . ' (`' . implode('`, `', array_keys($data)) . '`) VALUES ' . '(\'' . implode('\', \'', $data) . '\')';
                db_query($query);
                unset($data['download_id']);
                continue;
            }
            $values_for_ins[] = '(\'' . implode('\', \'', $data) . '\')';
        }
    }

    if (empty($values_for_ins)) {
        return $return;
    }


    $query = 'INSERT INTO ' . $tables['ppd_downloads'] . ' (`' . implode('`, `', array_keys($data)) . '`) VALUES ' . implode(', ', $values_for_ins);
    db_query($query);

    return $return;
}

function cw_ppd_doc_change_status_D(&$doc_data, &$return) {
    global $tables, $config;

    $doc_data['doc_id'] = (int) $doc_data['doc_id'];

    $query = 'DELETE FROM ' . $tables['ppd_downloads'] . ' WHERE order_id = \'' . $doc_data['doc_id'] . '\'';
    db_query($query);

    return $return;
}

function cw_ppd_doc_delete($doc_id) {
    global $tables, $config;

    $doc_id = (int) $doc_id;

    $query = 'DELETE FROM ' . $tables['ppd_downloads'] . ' WHERE order_id = \'' . $doc_id . '\'';
    db_query($query);

    $return = cw_get_return();
    return $return;
}

function cw_ppd_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {
    global $tables;

    if ($delete_all == true) {
        db_query('TRUNCATE TABLE ' . $tables['ppd_files']);
        db_query('TRUNCATE TABLE ' . $tables['ppd_downloads']);
        db_query('TRUNCATE TABLE ' . $tables['ppd_stats']);
    } else {
        $product_id = (int) $product_id;
        if (!empty($product_id)) {
            $product_id_condition = 'product_id = \'' . $product_id . '\'';
            db_query('DELETE FROM ' . $tables['ppd_files'] . ' WHERE ' . $product_id_condition);
            db_query('DELETE FROM ' . $tables['ppd_downloads'] . ' WHERE ' . $product_id_condition);
            db_query('DELETE FROM ' . $tables['ppd_stats'] . ' WHERE ' . $product_id_condition);
        }
    }
}

/**
 * Clone files when product cloned
 * POST hook
 * @see include/func/cw.product.php: cw_product_clone()
 * 
 */
function cw_ppd_product_clone($product_id) {
    $new_product_id = cw_get_return();
    if (!empty($new_product_id))
		cw_core_copy_tables('ppd_files', 'product_id', $product_id, $new_product_id);
    return $new_product_id;    
}

function cw_ppd_user_delete($params, $return) {
    global $tables, $config, $addons;

    extract($params);

    $customer_id = isset($customer_id) ? (int) $customer_id : 0;

    if (!empty($customer_id)) {
        $query = 'DELETE FROM ' . $tables['ppd_downloads'] . ' WHERE customer_id = \'' . $customer_id . '\'';
        db_query($query);
    }
}

function cw_ppd_tabs_js_abstract($params, $return) {
    global $is_ppd_files;

    if ($return['name'] == 'product_data' && AREA_TYPE == 'A') {
        if (!isset($return['js_tabs']['ppd'])) {
            $return['js_tabs']['ppd'] = array(
                'title' => cw_get_langvar_by_name('lbl_ppd_addon_title'),
                'template' => 'addons/ppd/admin/main.tpl',
            );
        }
    }

    if ($return['name'] == 'product_data_customer' && (isset($is_ppd_files) && $is_ppd_files == true) && AREA_TYPE == 'C') {
        if (!isset($return['js_tabs']['ppd'])) {
            $return['js_tabs']['ppd'] = array(
                'title' => cw_get_langvar_by_name('lbl_ppd_tab_title'),
                'template' => 'addons/ppd/customer/main.tpl',
            );
        }
    }

    return $return;
}

function cw_ppd_as3_real_url($as3_path, $lifetime = 1440) {

    global $config, $app_main_dir;
    if (empty($as3_path)) return '';

    include_once $app_main_dir.'/include/lib/AmazonS3/S3.php';
    $s3 = new S3($config['ppd']['ppd_awsAccessKey'], $config['ppd']['ppd_awsSecretKey']);
    $bucketName = $config['ppd']['ppd_aws3_bucketName'];
    return $s3->getAuthenticatedURL($bucketName, str_replace("aws3://",'',$as3_path), $lifetime*60);
}
