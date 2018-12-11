<?php
function cw_datascraper_wget_install () {
    global $tables, $app_dir;

    $WGET_DOWNLOADS_PATH = $app_dir."/files/DataScraper/wget_rec";
    if (!file_exists($WGET_DOWNLOADS_PATH)) {
        if (!mkdir($WGET_DOWNLOADS_PATH, 0777, true))
            die("Cant create directory $WGET_DOWNLOADS_PATH");  
    }    

    $wget_sites = array();
    $saved_sites = cw_query("SELECT * FROM $tables[datascraper_sites_config] WHERE parsed=0"); 
    if (!empty($saved_sites)) {
        foreach ($saved_sites as $site_data) {
            $wget_sites[] = array(
                'url' => $site_data['name'],
                'active' => $site_data['active'],
                'cron_period' => "0 $site_data[wget_run_hrs] * * $site_data[wget_run_day]"
            );
        }
    }


    $output = shell_exec('crontab -l');

    $return = array('msg'=>'', 'code'=>0);
    if (empty($output)) {
        $return['msg'] = 'Empty crontab -l output';
        return $return;
    }
    $cronlines = explode("\n", $output);

    $test_string = 'wget -r -p -U Mozilla';
    $wget_sample = "wget -r -p -U Mozilla --retry-connrefused -o ".$WGET_DOWNLOADS_PATH."/{{wget_log_name}}_wget_log.txt -P ".$WGET_DOWNLOADS_PATH." --reject jpeg,css,js,png,gif,jpg,tiff,bmp,txt,axd,ico{{products_exclude}} http://{{site_url}}";

    $complete_cron = array();
    foreach ($cronlines as $cr_line) {
        if (strpos($cr_line, $test_string) === false && !empty($cr_line)) {
            $complete_cron[] = $cr_line;
        }
    }

    foreach ($wget_sites as $wg_site) {
        if ($wg_site['active']) {
            $wg_site['url'] = str_replace('http://','',$wg_site['url']);
            $log_name = str_replace(array('www.', '.com', '@', '/', '%20'), '', $wg_site['url']);
            $complete_cron[] = $wg_site['cron_period'].' '.str_replace(array('{{wget_log_name}}', '{{products_exclude}}', '{{site_url}}'), array($log_name, $wg_site['products_exclude'] , $wg_site['url']), $wget_sample);
        }
    }

    $complete_cron_text = implode("\n", $complete_cron);

    $complete_cron_text .= "\n"; 

    $temp_cron_file = $WGET_DOWNLOADS_PATH."/tmp_cron.txt";
    if (file_exists($temp_cron_file))
        unlink($temp_cron_file);

    file_put_contents($temp_cron_file, $complete_cron_text);
    if (file_exists($temp_cron_file)) {
        $return['msg'] = exec('crontab '.$temp_cron_file).'<br />New crontab file: <br />'.str_replace("\n",'<br />',shell_exec('crontab -l'));
        $return['code'] = 1;
    } else
        $return['msg'] = 'Cant write the file'.$temp_cron_file."<br/>\n";

    return $return;
}

$result = cw_datascraper_wget_install();

if ($result['code']) {
    print("Wget re-config completed successfully.<br/>");
} else {
    print("Errors occured during Wget re-config.<br/>");    
}
print($result['msg']);



die;
