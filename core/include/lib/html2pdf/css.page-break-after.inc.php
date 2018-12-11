<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.page-break-after.inc.php,v 1.2 2008-06-08 18:29:02 cvs Exp $

class CSSPageBreakAfter extends CSSPageBreak {
  function getPropertyCode() {
    return CSS_PAGE_BREAK_AFTER;
  }

  function getPropertyName() {
    return 'page-break-after';
  }
}

CSS::register_css_property(new CSSPageBreakAfter);

?>