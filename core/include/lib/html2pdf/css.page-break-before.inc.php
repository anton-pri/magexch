<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.page-break-before.inc.php,v 1.2 2008-06-08 18:29:02 cvs Exp $

class CSSPageBreakBefore extends CSSPageBreak {
  function getPropertyCode() {
    return CSS_PAGE_BREAK_BEFORE;
  }

  function getPropertyName() {
    return 'page-break-before';
  }
}

CSS::register_css_property(new CSSPageBreakBefore);

?>