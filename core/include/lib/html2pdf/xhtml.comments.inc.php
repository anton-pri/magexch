<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/xhtml.comments.inc.php,v 1.2 2008-06-08 18:29:06 cvs Exp $

function remove_comments(&$html) {
  $html = preg_replace("#<!--.*?-->#is","",$html);
  $html = preg_replace("#<!.*?>#is","",$html);
}

?>