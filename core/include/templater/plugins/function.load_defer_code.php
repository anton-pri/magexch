<?php

function cw_cssurl_domain_select_domain($found) {
    global $HTTPS, $config, $app_web_dir, $app_config_file;

    static $avail_img_domains;

    if (is_null($avail_img_domains)) {
        $avail_img_domains = explode("\n", $config['performance']['list_available_cdn_servers']);
        //$avail_img_domains[]  = $app_config_file['web']['http_host'];
        //$avail_img_domains = array_unique($avail_img_domains);
    }

    $fname_hash = crc32($found[3]);

    $dom_id = $fname_hash % count($avail_img_domains);
    $img_domain = $avail_img_domains[$dom_id];

    $https_ltr = ($HTTPS) ? "s" : "";

	$img_domain = trim(str_replace('http' . $https_ltr . '://', '', $img_domain), '\/');

    $result = "url(http$https_ltr://" . trim($img_domain,"/") . $app_web_dir . $found[1];

    return $result;
}

/**
 * Defer loader plugin.
 *
 * @param array  $params should have 'type' element
 * @param Smarty $smarty Smarty object
 *
 * @return string always empty string
 */
function smarty_function_load_defer_code($params, &$smarty) {
    global $var_dirs, $app_dir, $app_skin_dir, $deferRegistry, $directInfoRegistry, $config, $var_dirs_web;

    if ($config['performance']['defer_load_js_code'] == "Y")
        $config['performance']['use_speed_up_js'] = "N";

    if (
        !isset($params['type'])
        || empty($params['type'])
        || !in_array($params['type'], array('js', 'css'))
    ) {
        return '';
    }

    $type = $params['type'];

    if (
        (!isset($deferRegistry[$type]) || empty($deferRegistry[$type]))
        && (!isset($directInfoRegistry[$type]) || empty($directInfoRegistry[$type]))
    ) {
        return '';
    }

    $type = $params['type'];
    $queue = array();

    if (isset($deferRegistry[$type])) {
        $queue = array_merge(array_keys($deferRegistry[$type]), $queue);
    }

    if (isset($directInfoRegistry[$type])) {
        $queue = array_merge(array_keys($directInfoRegistry[$type]), $queue);
    }
    sort($queue);

    if (
        isset($config['performance']['use_speed_up_' . $type])
        && 'Y' == $config['performance']['use_speed_up_' . $type]
        && defined('AREA_TYPE')
        && ('C' == constant('AREA_TYPE') || 'A' == constant('AREA_TYPE'))
    ) {
        $maxFtime = 0;
        $queue = array_unique($queue);

        foreach ($queue as $elem) {

             if (isset($deferRegistry[$type][$elem])) {

                foreach ($deferRegistry[$type][$elem] as $file) {
                    $ftime = intval(filemtime($file));
                    $maxFtime = max($maxFtime, $ftime);
                }
            }
        }

        $md5Suffix = md5(
            serialize($deferRegistry[$type])
            . (!empty($directInfoRegistry[$type]) ? serialize($directInfoRegistry[$type]) : '')
            . $maxFtime
            . $app_skin_dir
        );

        $cacheFile = $var_dirs['cache'] . DIRECTORY_SEPARATOR . '_' . $md5Suffix . '.' . $type;
        $cacheWebFile = $var_dirs_web['cache'] . '/' . '_' . $md5Suffix . '.' . $type;

        if (!is_file($cacheFile)) {
            $fp = @fopen($cacheFile, 'w');

            foreach ($queue as $elem) {
                $cache = '';

                if (
                    isset($deferRegistry[$type][$elem])
                    && !empty($deferRegistry[$type][$elem])
                ) {

                    foreach ($deferRegistry[$type][$elem] as $web => $file) {
                        $dir =  '../../..' . dirname($web) . '/';
                        $fileSource = file_get_contents($file);

                        if ('css' == $type) {
                            // Remove " and ' from URI path
                            $fileSource = preg_replace('/(url\()[\'" ]*([^)\'"]*)[\'" ]*(\))/', '\1\2\3', $fileSource);
                            // Add path to var/cache
                            $fileSource = preg_replace('/(url\()(?!http|data\:image|\/)(.*)/S', '\1' . $dir . '\2', $fileSource);
                           
                            if ($config['performance']['list_available_cdn_servers']) {
                                $srch_keyword = "/url\(..\/..\/..((" . str_replace("/", "\/", $app_skin_dir) . '\/)([^"})\'>]+))/i';
                                $fileSource = preg_replace_callback($srch_keyword , "cw_cssurl_domain_select_domain", $fileSource);
                            }
                        }
                        $cache .= "\n/*\n *  Source: $file\n */\n";
                        $cache .= $fileSource;
                    }
                    unset($deferRegistry[$type][$elem]);
                }

                if (
                    isset($directInfoRegistry[$type][$elem])
                    && !empty($directInfoRegistry[$type][$elem])
                ) {

                    foreach ($directInfoRegistry[$type][$elem] as $id => $value) {
                        $cache .= "\n/*\n *  Source direct code: $id\n */\n";
                        $cache .= $value;
                    }
                }

                if (
                    '' !== $cache
                ) {
                    @fwrite($fp, $cache);
                }
            } // foreach ($queue as $elem)
            @fclose($fp);
        }

        unset($deferRegistry[$type]);
        unset($directInfoRegistry[$type]);

        $result = ('js' == $type)
            ? '<script type="text/javascript" src="' . $cacheWebFile . '"></script>'
            : '<link rel="stylesheet" type="text/css" href="' . $cacheWebFile . '" />';

    }
    else {
        $result = '';
    }

    return $result;
}
