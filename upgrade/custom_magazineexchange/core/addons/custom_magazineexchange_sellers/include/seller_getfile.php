<?php

use setasign\Fpdi;

include_once $app_main_dir.'/include/lib/TCPDF/tcpdf.php';
require_once($app_main_dir.'/include/lib/FPDI/src/autoload.php');
require_once($app_main_dir.'/include/lib/FPDI_parser/src/autoload.php');

class WatermarkerTCPDF extends Fpdi\TcpdfFpdi {
    public $pdf, $file, $newFile,
    //        $wmText = "STACKOVERFLOW",
            $fontsize_default = 12;
    //        $fontfamily = 'ptsansnarrow400';

    /** $file and $newFile have to include the full path. */
    public function __construct($file = null, $newFile = null) {
        $this->pdf = new Fpdi\TcpdfFpdi();
        //custom fonts
        //$this->fontfamily = $this->pdf->addTTFfont(APPPATH . 'third_party/tcpdf/ttf/ptsansnarrow400.ttf', 'TrueTypeUnicode', '');
        if (!empty($file)) {
            $this->file = $file;
        }
        if (!empty($newFile)) {
            $this->newFile = $newFile;
        }
    }

    protected function _printText($page_idx, $text, $X, $Y, $cX, $cY) {
        $tplidx = $this->pdf->importPage($page_idx);
        $specs = $this->pdf->getTemplateSize($tplidx);
        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetPrintFooter(false);
        $this->pdf->addPage($specs['width'] > $specs['height'] ? 'L' : 'P', array($specs['width'], $specs['height']));
/*
var_dump($specs); 
print('<pre>');
print_r($this->pdf);
print('</pre>');
die;
*/
        $xk = $specs['height']/297;
        $fontsize = $xk*($this->fontsize_default);
        $X *= $xk;
        $Y *= $xk;
        $cX *= $xk;
        $cY *= $xk;  

        //$this->pdf->useTemplate($tplidx, null, null, $specs['width'], $specs['height'], false);
        $this->pdf->useTemplate($tplidx, 0, 0, $specs['width'], $specs['height'], true);

        $this->pdf->SetFont($this->fontfamily, '', $fontsize);
        $this->pdf->SetTextColor(0, 0, 0);
            //$this->pdf->SetXY($specs['w']/2, $specs['h']/2);
        $_x = ($specs['width']/2) - ($this->pdf->GetStringWidth($this->wmText, $this->fontfamily, '', $fontsize)/2.8);
        $_y = $specs['height']/2;
            //$this->pdf->SetXY($_x, $_y);
//        $this->pdf->SetXY($X, $Y);
        $this->pdf->setAlpha(1);
/*
        $this->_rotate(90, $cX, $cY);
        $this->pdf->Write(0, $text);
*/
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, $cX, $cY);
        $this->pdf->Text($X, $Y, $text);
        $this->pdf->StopTransform();
    }

    /** @todo Make the text nicer and add to all pages */
    public function doWaterMark() {
        $currentFile = $this->file;
        $newFile = $this->newFile;

        $pagecount = $this->pdf->setSourceFile($currentFile);

        for ($i = 1; $i <= $pagecount; $i++) {
             $this->_printText($i, $this->wmText, 0, 3, 125, 125);
        }

        if (empty($newFile)) {
            header('Content-Type: application/pdf');
            $this->pdf->Output();
        } else {
            $this->pdf->Output($newFile, 'F');
        }
    }

    protected function _rotate($angle, $x = -1, $y = -1) {
        if ($x == -1)
            $x = $this->pdf->x;
        if ($y == -1)
            $y = $this->pdf->y;
        //if ($this->pdf->angle != 0)
            //$this->pdf->_out('Q');
        $this->pdf->angle = $angle;
        if ($angle != 0) {
            $angle*=M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->pdf->k;
            $cy = ($this->pdf->h - $y) * $this->pdf->k;

            $this->pdf->_out(sprintf(
                            'q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }

    }

    public function wmText($text = null)
    {
        $this->wmText = $text;
        return $this;
    }
}

if (empty($customer_id) && $current_area == 'C') { 
    if (!empty($doc_id)) 
        cw_header_location("index.php?target=docs_O&mode=details&doc_id=$doc_id");
    else
        cw_header_location("index.php?target=docs_O&mode=search"); 
}

if (empty($seller_item_id) || empty($customer_id)) {
    die("<p />Download link parameters are not valid<p />");
    //cw_header_location("index.php?target=error_message&error=access_denied&id=33");
}

if ($current_area == 'C') 
    $ppd_file_info = cw_query_first("SELECT mspd.seller_item_id, mspd.product_id, av.value as file, d.doc_id, d.display_id, (UNIX_TIMESTAMP(NOW()) - d.date) as age FROM $tables[magazine_sellers_product_data] mspd INNER JOIN $tables[docs_items] di ON di.product_id=mspd.product_id INNER JOIN $tables[docs] d ON d.doc_id=di.doc_id INNER JOIN $tables[order_statuses] os ON os.code=d.status and os.inventory_decreasing = 1 INNER JOIN $tables[docs_user_info] dui ON dui.doc_info_id = d.doc_info_id and dui.customer_id ='$customer_id' INNER JOIN $tables[attributes_values] av ON av.item_id = mspd.seller_item_id and av.item_type='SP' INNER JOIN $tables[attributes] a ON av.attribute_id = a.attribute_id and a.field = 'seller_product_main_file' where mspd.seller_item_id='$seller_item_id' and mspd.is_digital=1");
elseif ($current_area == 'V') 
    $ppd_file_info = cw_query_first("SELECT mspd.seller_item_id, mspd.product_id, av.value as file FROM $tables[magazine_sellers_product_data] mspd INNER JOIN $tables[attributes_values] av ON av.item_id = mspd.seller_item_id and av.item_type='SP' INNER JOIN $tables[attributes] a ON av.attribute_id = a.attribute_id and a.field = 'seller_product_main_file' WHERE mspd.seller_id = '$customer_id' AND mspd.seller_item_id='$seller_item_id' and mspd.is_digital=1");


if (empty($ppd_file_info)) {
    die("<p />Download link parameters are not valid<p />");
    //cw_header_location("index.php?target=error_message&error=access_denied&id=34");
}



/*
if (substr($ppd_file_info['file'], 0, 7) == 'aws3://') {
    $expire_period = $config['ppd']['ppd3_aws_item_lifetime']*60;
} else {
    $expire_period = $config['ppd']['ppd_link_lifetime']*3600;
}

if ($expire_period && ($expire_period < $ppd_file_info['age'])) {
    die('<p />Download link is expired<p />');
    //cw_header_location("index.php?target=error_message&error=access_denied&id=35");
}    
*/

$ppd_file_info['real_url'] = cw_ppd_as3_real_url($ppd_file_info['file']);
//cw_header_location($ppd_file_info['real_url']);

$ppd_file_info['local_tmp_file'] = md5($ppd_file_info['file'].$seller_item_id);

$ppd_file_info['full_local_tmp_file'] = $var_dirs['clear_cache'].'/sellers_temp_files/'.$ppd_file_info['local_tmp_file'];

if (!file_exists($var_dirs['clear_cache'].'/sellers_temp_files'))
   mkdir($var_dirs['clear_cache'].'/sellers_temp_files'); 

if (!file_exists($ppd_file_info['full_local_tmp_file'])) {
   copy($ppd_file_info['real_url'], $ppd_file_info['full_local_tmp_file']);
}

if (!file_exists($ppd_file_info['full_local_tmp_file']))
    die('Cant copy file from remote storage');
else
    $ppd_file_info['local_tmp_filesize'] = filesize($ppd_file_info['full_local_tmp_file']);

$ppd_file_info['extension'] = pathinfo($ppd_file_info['file'], PATHINFO_EXTENSION);
$ppd_file_info['filename'] = pathinfo($ppd_file_info['file'], PATHINFO_BASENAME);

/*
print('<pre>');
print_r($ppd_file_info);
print('</pre>');
die;
*/
$userinfo = cw_user_get_info($customer_id, 1);

$wm_enabled = false;
if (strtolower($ppd_file_info['extension']) == 'pdf') {
    $wm_enabled = cw_query_first_cell( $s = "SELECT COUNT(*) FROM $tables[attributes_default] ad INNER JOIN $tables[attributes] a ON a.attribute_id = ad.attribute_id AND a.field='seller_product_file_type' INNER JOIN $tables[attributes_values] av ON a.attribute_id = av.attribute_id AND av.item_id='$seller_item_id' AND ad.attribute_value_id = av.value WHERE ad.value_key=1 OR ad.value LIKE ('%watermark%')");
}

    $file = $ppd_file_info['full_local_tmp_file'];
    $file_info = array(
        'type' => 'application/octet-stream',
        'size' => $ppd_file_info['local_tmp_filesize'],
        'filename' => $ppd_file_info['filename'] 
    );


if ($wm_enabled) {
    $file_path = $ppd_file_info['full_local_tmp_file']; 
    $file_path_wm = $file_path.'_wm';

    if (file_exists($file_path_wm))
        @unlink($file_path_wm);

    try {
        $watermark = new WatermarkerTCPDF($file_path, $file_path_wm);
        $watermark->wmText("Copyrighted item purchased from www.magazineexchange.co.uk by: ".ucwords("$userinfo[firstname] $userinfo[lastname]")." $userinfo[email]");
        $watermark->doWaterMark();
    } catch (Exception $e) {
        if ($test_mode == "Y") {  
            print("FPDI ERROR CATCHED:<br>");
            print($e->getMessage()."<p >");  
            print("The watermark is not applied if the document is downloaded by the customer, the originally uploaded file is provided to them instead. Please change the file type to 'PDF' option to avoid this message on the 'Test Download' page.<p >"); 
            print("<a href='index.php?target=seller_getfile&seller_item_id=$seller_item_id'>Download without watermark</a>");         
            exit;
        } else {

        }  
    }

    if (file_exists($file_path_wm)) {
        $file = $file_path_wm;
        $file_info = array(
            'type' => 'application/octet-stream',
            'size' => filesize($file),
            'filename' => str_replace(' .','.',$ppd_file_info['filename'])
        );
    }
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

if ($file_info['size'] > $chunksize) {
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

exit(0);
