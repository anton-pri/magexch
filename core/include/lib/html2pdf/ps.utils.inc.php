<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/ps.utils.inc.php,v 1.2 2008-06-08 18:29:05 cvs Exp $

function trim_ps_comments($data) {
  $data = preg_replace("/(?<!\\\\)%.*/","",$data);
  return preg_replace("/ +$/","",$data);
}

function format_ps_color($color) {
  return sprintf("%.3f %.3f %.3f",$color[0]/255,$color[1]/255,$color[2]/255);
}
?>