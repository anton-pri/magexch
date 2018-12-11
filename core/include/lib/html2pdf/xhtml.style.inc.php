<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/xhtml.style.inc.php,v 1.3 2009-07-28 20:57:17 cvs Exp $

function process_style(&$html) {
  $styles = array();

# kornev, wrong preg
//  if (preg_match('#^(.*)(<style[^>]*>)(.*?)(</style>)(.*)$#is', $html, $matches)) {
  if (preg_match_all('/(<style[^>]*>)(.*?)(<\/style>)/is', $html, $matches)) {
    foreach($matches[2] as $k=>$v)
        $styles[] = $matches[1][$k].process_style_content($v).$matches[3][$k];
    $html = preg_replace('/(<style[^>]*>)(.*?)(<\/style>)/is', '', $html);
  };

  return $styles;
}

function process_style_content($html) {
  // Remove CDATA comment bounds inside the <style>...</style> 
  $html = preg_replace("#<!\[CDATA\[#","",$html); 
  $html = preg_replace("#\]\]>#is","",$html);

  // Remove HTML comment bounds inside the <style>...</style> 
  $html = preg_replace("#<!--#is","",$html); 
  $html = preg_replace("#-->#is","",$html);

  // Remove CSS comments
  $html = preg_replace("#/\*.*?\*/#is","",$html);

  // Force CDATA comment
  $html = '<![CDATA['.$html.']]>';

  return $html;
}

function insert_styles($html, $styles) {
  // This function is called after HTML code has been fixed; thus, 
  // HEAD closing tag should be present

  $html = preg_replace('#</head>#', join("\n", $styles)."\n</head>", $html);
  return $html;
}

?>
