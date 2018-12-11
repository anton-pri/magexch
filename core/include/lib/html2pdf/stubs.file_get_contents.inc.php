<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/stubs.file_get_contents.inc.php,v 1.2 2008-06-08 18:29:05 cvs Exp $

function file_get_contents($file) {
  $lines = file($file);
  if ($lines) {
    return implode('',$lines);
  } else {
    return "";
  };
}
?>