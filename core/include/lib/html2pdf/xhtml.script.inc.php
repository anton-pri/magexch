<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/xhtml.script.inc.php,v 1.2 2008-06-08 18:29:06 cvs Exp $

function process_script($sample_html) {
  return preg_replace("#<script.*?</script>#is","",$sample_html);
}

?>