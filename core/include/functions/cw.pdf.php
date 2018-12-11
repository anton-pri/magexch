<?php
global $var_dirs;
include_once $app_main_dir.'/include/lib/html2pdf/config.inc.php';
include_once HTML2PS_DIR.'pipeline.factory.class.php';

class MyFetcherMemory extends Fetcher {
  var $base_path;
  var $content;

  function MyFetcherMemory($content, $base_path) {
    $this->content   = $content;
    $this->base_path = $base_path;
  }

  function get_data($url) {
    if (!$url) {
      return new FetchedDataURL($this->content, array(), "");
    } else {
      // remove the "file:///" protocol
      if (substr($url,0,8)=='file:///') {
        $url=substr($url,8);
        // remove the additional '/' that is currently inserted by utils_url.php
        if (PHP_OS == "WINNT") $url=substr($url,1);
      }
      return new FetchedDataURL(@file_get_contents($url), array(), "");
    }
  }

  function get_base_url() {
    return $this->base_path;
  }
}

class MyDestinationFile extends Destination {
  var $_dest_filename;

  function MyDestinationFile($dest_filename) {
    $this->_dest_filename = $dest_filename;
  }

  function process($tmp_filename, $content_type) {
    copy($tmp_filename, $this->_dest_filename);
  }
}

function cw_pdf_generate($file, $template, $save_to_file = false, $landscape = false, $pages_limit = 0, $page_margins = array('10', '10', '10', '10'), $show_pages = true) {
    global $smarty, $var_dirs, $current_location;

    set_time_limit(2700);
    ini_set('memory_limit', '512M');

    $smarty->assign('is_pdf', true);

# kornev, only for A4 && 1024 p/wide
    $wcr = $hcr = 1024/210;
    $smarty->assign('wcr', $wcr);
    $smarty->assign('hcr', $hcr);

    if ($save_to_file && $file)
        $html = $file;
    else
        $html = cw_display($template, $smarty, false);
  
    parse_config_file(HTML2PS_DIR.'html2ps.config');

    $pipeline = PipelineFactory::create_default_pipeline('', '');

    $pipeline->fetchers[] = new MyFetcherMemory($html, $current_location);
    if ($save_to_file)
        $pipeline->destination = new MyDestinationFile($save_to_file);
    else
        $pipeline->destination = new DestinationDownload($file);

    if ($show_pages)
        $pipeline->pre_tree_filters[] = new PreTreeFilterHeaderFooter('', '<div>'.cw_get_langvar_by_name('lbl_page', null, false, true).' ##PAGE## / ##PAGES## </div>');
    
    $pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields();

    $media =& Media::predefined('A4');
    $media->set_landscape($landscape);
    $media->set_margins(array('left'   => $page_margins[3],
                            'right'  => $page_margins[1],
                            'top'    => $page_margins[0],
                            'bottom' => $page_margins[2]));
    $media->set_pixels(1024);

    $g_config = array(
                    'cssmedia'     => 'print',
                    'scalepoints'  => '1',
                    'renderimages' => true,
                    'renderlinks'  => false,
                    'renderfields' => false,
                    'renderforms'  => false,
                    'mode'         => 'html',
                    'encoding'     => '',
                    'debugbox'     => false,
                    'pdfversion'   => '1.4',
                    'smartpagebreak' => true,
                    'draw_page_border' => false,
                    'html2xhtml' => false,
                    'method' => 'fpdf',
                    'pages_limit' => $pages_limit,
                    );
    $pipeline->configure($g_config);
    $pipeline->process_batch(array(''), $media);

    if (!$save_to_file)
        exit(0);
}
?>
