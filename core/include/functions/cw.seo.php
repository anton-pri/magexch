<?php
if (!defined('APP_START')) die('Access denied');

function cw_write_site_map($url, $mode = 0) {
    global $smarty;
    global $site_map_counter;
    global $file_counter;

    $date = date("Y-m-d", cw_core_get_time()); 

    if (!isset($file_counter)) $file_counter = 1;

    $tmp_filename = $smarty->compile_dir.'/sitemap'.$file_counter.'.xml';
    $tmp_filename_yahoo = $smarty->compile_dir.'/urllist.txt';

# kornev, google sitemap
    if ($site_map_counter == 49999 || $mode == 2) {
        $site_map_counter = 0;
        $mode = 2;
        $file_counter++;
    }
    if ($site_map_counter == 1) $mode = 1;
    
    if ($mode == 1) {
        $fp = fopen($tmp_filename, "w+");
$str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">";
        fwrite($fp, $str);
    }
    else $fp = fopen($tmp_filename, "a+");

    $str = <<<EOT

<url>
<loc>$url</loc>
<lastmod>$date</lastmod>
<changefreq>daily</changefreq>
<priority>0.5</priority>
</url>
EOT;
    if (!empty($url))
        fwrite($fp, $str);

    if ($mode == 2)
        fwrite($fp, "\n</urlset>");

    fclose($fp);
    $site_map_counter++;

# kornev, yahoo sitemap
    if (!empty($url)) {
        if ($mode == 1)
            $fp = fopen($tmp_filename_yahoo, "w+");
        else
            $fp = fopen($tmp_filename_yahoo, "a+");
        fwrite($fp, $url."\n");
        fclose($fp);
    }
}

function cw_commit_sitemap() {
    global $site_map_counter;
    global $file_counter;
    global $smarty, $tables;
    global $app_catalogs, $var_dirs;

# kornev, commit google sitemap
    $site_map_name = $smarty->compile_dir.'/sitemap.xml'; 
    $files = array();
    for($i=1; $i<=$file_counter; $i++) {
        $tmp_filename = $smarty->compile_dir.'/sitemap'.$i.'.xml';
        $sitemap_file = $var_dirs['sitemap'].'/sitemap'.$i.'.xml.gz';
        if (is_file($tmp_filename)) {
            @unlink($sitemap_file);
            @rename($tmp_filename, $site_map_name);
            exec($sql="gzip -c ".$site_map_name." > ".$sitemap_file);
    //        @unlink($site_map_name);
            $files[] = $sitemap_file;
        }
    }

    $tmp_filename = $smarty->compile_dir.'/sitemap_index.xml';
    $fp = fopen($tmp_filename, "w+");
    $str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<sitemapindex xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";
    fwrite($fp, $str);

    $date = date("Y-m-d", cw_core_get_time());
    foreach($files as $file) {
        $str = "<sitemap>
<loc>".$app_catalogs['customer']."/".basename($file)."</loc>
<lastmod>".$date."</lastmod>
</sitemap>\n";
        fwrite($fp, $str);
    }
    $str = "</sitemapindex>";
    fwrite($fp, $str);

    @rename($tmp_filename, $var_dirs['sitemap'].'/sitemap_index.xml');

# kornev, commit yahoo sitemap
    $tmp_filename_yahoo = $smarty->compile_dir.'/urllist.txt';
    @rename($tmp_filename_yahoo, $var_dirs['sitemap'].'/urllist.txt');

    db_query("UPDATE $tables[config] SET value='".(time())."' WHERE name='google_sitemap_date'");
}
?>
