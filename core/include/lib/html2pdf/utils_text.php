<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/utils_text.php,v 1.2 2008-06-08 18:29:06 cvs Exp $

function squeeze($string) {
  return preg_replace("![ \n\t]+!"," ",trim($string));
}

?>