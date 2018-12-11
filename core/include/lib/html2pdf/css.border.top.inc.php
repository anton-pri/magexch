<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.border.top.inc.php,v 1.2 2008-06-08 18:29:01 cvs Exp $

class CSSBorderTop extends CSSSubFieldProperty {
  function getPropertyCode() {
    return CSS_BORDER_TOP;
  }

  function getPropertyName() {
    return 'border-top';
  }

  function parse($value) {
    if ($value == 'inherit') {
      return CSS_PROPERTY_INHERIT;
    };

    $border = CSSBorder::parse($value);
    return $border->left;
  }
}

?>