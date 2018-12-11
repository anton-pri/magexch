<?php
if (!defined('APP_START')) die('Access denied');

function cw_cpanel_get_stores() {
    global $tables;
    
    return cw_query("select * from $tables[accounts]");
}

function cw_cpanel_get_dirs() {
    global $app_main_dir, $var_dirs;

    $stores = cw_cpanel_get_stores();
    $ret = array(
        'php' => $app_main_dir,
        'tpl' => array($var_dirs['repository']),
    );
    if ($stores)
        foreach($stores as $val)
            if ($val['path'])
                $ret['tpl'][] = $val['path'];
    return $ret;
}

function cw_cpanel_get_databases() {
    $stores = cw_cpanel_get_stores();
    $ret = array();
    if ($stores)
        foreach($stores as $val) {
            $config_file = $val['path'].'/include/config.ini';
            if ($val['path'] && is_file($config_file)) {
                $app_config_file = parse_ini_file($config_file, true);
                $ret[] = $app_config_file['sql'];
            }
        }
    return $ret;
}

function cw_cpanel_check_fields($fields, &$check) {
    $check = array();

    $check['path'] = is_writable($fields['path']);

    $mylink = @mysql_connect($fields['mysql_host'], $fields['mysql_user'], $fields['mysql_password'], true);
    $check['mysql_host'] = $mylink?true:false;
    if ($mylink)
        $check['mysql_database'] = @mysql_select_db($fields['mysql_db'], $mylink)?true:false;

    if ($check['path'])
        $check['skins'] = is_dir($fields['path'].'/skins');
    if ($check['mysql_database']) {
        $res = @mysql_list_tables($fields['mysql_db'], $mylink);

        $check['tables'] = false;
        while ($row = @mysql_fetch_row($res)) {
            $ctable = $row[0];
            if ($ctable == 'ars_products' || $ctable == 'ars_customers') {
                $check['tables'] =  true;
                break;
            }
        }
    }
    @mysql_close($mylink);

    $required_fields = array('path', 'mysql_host', 'mysql_database');
    $is_no_error = true;
    foreach($required_fields as $val)
        $is_no_error &= $check[$val];

    return $is_no_error; 
}

function cw_cpanel_install_start() {
    global $smarty;

    $txt_cpanel_site_title = cw_get_langvar_by_name("txt_cpanel_site_title", null, false, true, true) ;
    $skin_dir = $smarty->_tpl_vars['SkinDir'];

    echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>$txt_cpanel_site_title</title>
<link rel="stylesheet" href="$skin_dir/styles.css" />
</head>
<body>
EOT;
}

function cw_cpanel_install_end() {
    echo <<<EOT
</body>
</html>
EOT;
}

function cw_cpanel_install_show_status($operation, $info, $status) {
    $operation = cw_get_langvar_by_name('lbl_installation_'.$operation, null, false, true, true);

    $label = '['.cw_get_langvar_by_name($status?'lbl_ok':'lbl_false', null, false, true, true).']';
    $status = $status?'true':'false';
    echo <<<EOT
<div class="InstallationOperationStatus">
$operation $info .... <span id="$status">$label</span>
</div>
EOT;
}

function cw_cpanel_install_files() {
    global $var_dirs;

    $args = func_get_args();
    $installation_path = array_shift($args);

    $ret = true;
    if (is_array($args))
        foreach($args as $file) {
            $file_path = $var_dirs['repository'].'/'.$file;
            $new_path = $installation_path.'/'.$file;
            if (is_file($file_path) && @copy($file_path, $new_path)) {
               cw_cpanel_install_show_status('copy', $file, true);
            }
            elseif(is_dir($file_path)) {
                $files = array();
                $res = cw_core_copy_dir($file_path, $new_path, $files);
                cw_cpanel_install_show_status('copy', $file, $res);
                if (is_array($files)) {
                    foreach($files as $file)
                        cw_cpanel_install_show_status('copy', $file, false);
                    $ret = false;
                }
            }
            else {
                cw_cpanel_install_show_status('copy', $file, false);
                $ret = false;
            }
        }
    return $ret;
}

function cw_cpanel_install_dirs() {
    $args = func_get_args();
    $installation_path = array_shift($args);
   
    $ret = true; 
    if (is_array($args))
        foreach($args as $dir) {
            $new_path = $installation_path.'/'.$dir;
            if (cw_core_create_dirs(array($new_path), $tmp)) 
                cw_cpanel_install_show_status('create_dir', $dir, true);
            else {
                cw_cpanel_install_show_status('create_dir', $file, false);
                $ret = false;
            }
        }
    return $ret;
}

function cw_cpanel_install_config($path, $params) {
    global $app_main_dir;
 
    $path = $path.'/include/config.ini';

    $arr = array(
        'cpanel' => array(
            'path' => $app_main_dir,
        ),
        'web' => array(
            'http_host' => $params['http_host'],
            'https_host' => $params['https_host'],
            'web_dir' => $params['web_dir']
        ),
        'sql' => array(
            'host' => $params['mysql_host'],
            'user' => $params['mysql_user'],
            'db' => $params['mysql_db'],
            'password' => $params['mysql_password']
        ),
    );
     
    foreach ($arr as $key => $elem) {
        $content .= "[".$key."]\n";
        foreach ($elem as $key2 => $elem2)
            $content .= $key2." = \"".$elem2."\"\n";
    }

    $ret = false;
    if ($handle = fopen($path, 'w'))
        if (fwrite($handle, $content))
            $ret = true;
    fclose($handle);

    cw_cpanel_install_show_status('create_config', $file, $ret);
    return $ret;
}

function cw_cpanel_install_htaccess($path, $params) {
    global $app_main_dir, $var_dirs;

    $file_path = $var_dirs['repository'].'/.htaccess';
    $new_path = $path.'/.htaccess';

    if (is_file($file_path) && @copy($file_path, $new_path)) {
        $content = file_get_contents($new_path);
        $content = preg_replace('/RewriteBase.*/U', 'RewriteBase '.$params['web_dir'], $content);
        if ($content) {
            file_put_contents($new_path, $content);
            cw_cpanel_install_show_status('create_htaccess', $file, true);
            return true;
        }
    }

    cw_cpanel_install_show_status('create_htaccess', $file, false);
    return false;
}

function cw_cpanel_install_tables($params) {
    global $var_dirs, $mysql_connection_id;

    $current_connection = $mysql_connection_id;

    $ret = @db_connect($params['mysql_host'], $params['mysql_user'], $params['mysql_password']) ;
    if ($ret) $ret &= db_select_db($params['mysql_db']);
    if (!$ret) {
        $mysql_connection_id = $current_connection;
        cw_cpanel_install_show_status('db_connect', $file, false);
        return false;
    }
    $new_connection = $mysql_connection_id;

    $file_path = $var_dirs['repository'].'/sql';
    $files = array(
        'ars_tables.sql',
        'ars_data.sql'
    );

    foreach($files as $file) {
        $fp = @fopen($file_path.'/'.$file, "rb");
        if ($fp === false) {
            $mysql_connection_id = $current_connection;
            cw_cpanel_install_show_status('read_db_file', $file, false);
            return false;
        }    
   
        $command = "";
        $counter = 0;

        while (!feof($fp)) {
            $c = chop(fgets($fp, 100000));
            $c = ereg_replace("^[ \t]*(#|-- |---*).*", "", $c);

            $command .= $c;

            if (ereg(";$", $command)) {
                $command = ereg_replace(";$", "", $command);

                if (ereg("CREATE TABLE ", $command)) {
                    $table_name = ereg_replace(" .*$", "", eregi_replace("^.*CREATE TABLE ", "", $command));

                    db_query($command);

                    $myerr = mysql_error();
                    if (!empty($myerr)) {   
                        $mysql_connection_id = $current_connection;
                        cw_cpanel_install_show_status('create_table', $table_name, false);
                        return false;
                    }
                    $mysql_connection_id = $current_connection;
                    cw_cpanel_install_show_status('create_table', $table_name, true);
                    $mysql_connection_id = $new_connection;
                } 
                else {
                    db_query($command);

                    $myerr = mysql_error();
                    if (!empty($myerr)) {
                        $mysql_connection_id = $current_connection;
                        cw_cpanel_install_show_status('sql_row', $table_name, false);
                        return false;
                    }
                    else {
                        $counter++;
                        if (!($counter % 50))
                            echo "."; flush();
                    }
                }
                $command = "";
                flush();
            }
        }
        fclose($fp);
    } 

    $mysql_connection_id = $current_connection;
    return true;
}

function cw_cpanel_install_complete($params) {
    global $tables;

    $to_insert = array (
        'path' => $params['path'],
        'title' => $params['title'],
        'http_host' => $params['http_host'],
        'web_dir' => $params['web_dir'], 
    );
    db_query("delete from $tables[accounts] where path='$to_insert[path]'");
    cw_array2insert('accounts', $to_insert);
    cw_cpanel_install_show_status('installation', '', true);
}
?>
