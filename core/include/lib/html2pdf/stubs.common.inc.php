<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/stubs.common.inc.php,v 1.2 2008-06-08 18:29:05 cvs Exp $

if (!function_exists('file_get_contents')) {
  require_once(HTML2PS_DIR.'stubs.file_get_contents.inc.php');
}

if (!function_exists('file_put_contents')) {
  require_once(HTML2PS_DIR.'stubs.file_put_contents.inc.php');
}

if (!function_exists('is_executable')) {
  require_once(HTML2PS_DIR.'stubs.is_executable.inc.php');
}

if (!function_exists('memory_get_usage')) {
  require_once(HTML2PS_DIR.'stubs.memory_get_usage.inc.php');
}

if (!function_exists('_')) {
  require_once(HTML2PS_DIR.'stubs._.inc.php');
}

?>