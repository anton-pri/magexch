<?php

if (!defined('APP_START')) {
    die('The software application has not been initialized.');
}

do {

    if (!isset($addons['ppd'])) {
        break;
    }

    global $file_id, $product_id;

    $file_id = isset($_GET['file_id']) ? (int) $_GET['file_id'] : 0;
    $product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

    if (empty($product_id)) {
        cw_header_location('index.php');
    }

    require_once $app_main_dir . '/addons/ppd/func.php';


    $_allow_downloading = false;
    $_init_time = &cw_session_register('_init_time');


    if (empty($_init_time) || (time() - $_init_time) <= 2) {
        break;
    }

    if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER'])) {
        break;
    }

    $_referrer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

    if (empty($_referrer_host) || ($app_config_file['web']['http_host'] != $_referrer_host || $app_config_file['web']['https_host'] != $_referrer_host)) {
        break;
    }

    $_allow_downloading = true;
} while (0);



if (empty($file_id) || $_SERVER['REQUEST_METHOD'] != 'GET' || defined('IS_ROBOT') || $_allow_downloading == false) {
    cw_header_location("index.php?target=product&product_id=$product_id");
}

$customer_id = isset($customer_id) ? (int) $customer_id : 0;

$file_info = array();
$file = null;

if (!empty($customer_id)) {
    $current_time = cw_core_get_time();
    $time_query_param = null;
    if (!empty($config['ppd']['ppd_link_lifetime'])) {
        $time_query_param = ' AND expiration_date > \'' . $current_time . '\'';
    }

    $counter_query_param = null;
    if (!empty($config['ppd']['ppd_loading_attempts'])) {
        $counter_query_param = ' AND dloads.allowed_number > dloads.counter';
    }

    $file_info = cw_query_first('SELECT files.filename, files.title, files.size, types.type, dloads.download_id FROM ' . $tables['ppd_files'] . ' AS files LEFT JOIN ' . $tables['ppd_downloads'] . ' AS dloads ON files.file_id = dloads.file_id LEFT JOIN ' . $tables['ppd_types'] . ' AS types ON files.type_id = types.type_id WHERE files.file_id = \'' . $file_id . '\' AND dloads.product_id = \'' . $product_id . '\' AND files.active = 1 AND files.perms_owner = 5 AND files.perms_all = 0 AND dloads.customer_id = \'' . $customer_id . '\'' . $counter_query_param . $time_query_param);
}

if (empty($file_info) || !is_array($file_info)) {
    $file_info = cw_query_first('SELECT filename, title, size, type FROM ' . $tables['ppd_files'] . ' AS files LEFT JOIN ' . $tables['ppd_types'] . ' AS types ON files.type_id = types.type_id WHERE file_id = \'' . $file_id . '\' AND product_id = \'' . $product_id . '\' AND active = 1 AND perms_all = 5');
}

if (empty($file_info) || !is_array($file_info)) {
    cw_header_location("index.php?target=product&product_id=$product_id");
}

if (empty($file_info['type'])) {
    $file_info['type'] = 'application/octet-stream'; //application/force-download
}

//$file = ppd_get_url_tofile($file_info['filename']);
$file = ppd_check_path($file_info['filename']);

if (empty($file)) {
    cw_header_location("index.php?target=product&product_id=$product_id");
} else {

    if (isset($file_info['download_id'])) {
        db_query('UPDATE ' . $tables['ppd_downloads'] . ' SET counter = counter + 1 WHERE download_id = \'' . $file_info['download_id'] . '\'');
    }

    $_data = array();
    $_data['file_id'] = $file_id;
    $_data['product_id'] = $product_id;
    $_data['period'] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $_data['counter'] = cw_query_first_cell('SELECT counter FROM ' . $tables['ppd_stats'] . ' WHERE file_id = \'' . $file_id . '\' AND product_id = \'' . $product_id . '\' AND period = \'' . $_data['period'] . '\'');

    if (empty($_data['counter'])) {
        $_data['counter'] = 1;
        cw_array2insert($tables['ppd_stats'], $_data);
    } else {
        db_query('UPDATE ' . $tables['ppd_stats'] . ' SET counter = counter + 1 WHERE file_id = \'' . $file_id . '\' AND product_id = \'' . $product_id . '\' AND period = \'' . $_data['period'] . '\'');
    }

    $mtime = ($mtime = filemtime($file)) ? $mtime : gmtime();

    @set_time_limit(3600);

    @ob_end_clean();
    if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 0);
    }
    if (function_exists('apache_setenv')) apache_setenv('no-gzip', 1);

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Description: File Transfer");
    header("Content-Type: $file_info[type]");
    if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") != false) {
        header("Content-Disposition: attachment; filename=" . urlencode($file_info['filename']) . '; modification-date="' . date('r', $mtime) . '";');
    } else {
        header("Content-Disposition: attachment; filename=\"" . $file_info['filename'] . '"; modification-date="' . date('r', $mtime) . '";');
    }
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: $file_info[size]");
//    header("Location: $file");

    $chunksize = 1 * (1024 * 1024);

    $_memory_limit = trim(ini_get('memory_limit'));
    if (!empty($_memory_limit)) {
        $_dim = strtolower(substr($_memory_limit, -1));
        $_dim = ($_dim == 'm' ? 1048576 : ($_dim == 'k' ? 1024 : ($_dim == 'g' ? 1073741824 : 1)));
        $_memory_limit = (int) $_memory_limit * $_dim;
    }

    if (!empty($_memory_limit) && $chunksize >= $_memory_limit) {
        $chunksize = ceil(($_memory_limit * 2) / 3);
    }

    if ($file_info[size] > $chunksize) {
        if ($handle = fopen($file, 'rb')) {
            $buffer = null;
            while (!feof($handle)) {
                $buffer = fread($handle, $chunksize);
                echo $buffer;
                flush();
            }
            fclose($handle);
        }
    } else {
        readfile($file);
    }
}
exit(0);
?>
