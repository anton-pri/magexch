<?php 
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/xhtml.selects.inc.php,v 1.2 2008-06-08 18:29:06 cvs Exp $

function process_option(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(option|/select|/option)", 
                       array(), 
                       "/option");  
};

function process_select(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(option|/select)", 
                       array("option" => "process_option"), 
                       "/select");  
};

function process_selects(&$sample_html, $offset) {
  return autoclose_tag($sample_html, $offset, "(select)", 
                       array("select" => "process_select"), 
                       "");  
};

?>