<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.page-break-inside.inc.php,v 1.2 2008-06-08 18:29:02 cvs Exp $

class CSSPageBreakInside extends CSSPageBreak {
  function getPropertyCode() {
    return CSS_PAGE_BREAK_INSIDE;
  }

  function getPropertyName() {
    return 'page-break-inside';
  }
}

CSS::register_css_property(new CSSPageBreakInside);

?>