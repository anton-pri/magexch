<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.border.bottom.inc.php,v 1.2 2008-06-08 18:29:01 cvs Exp $

class CSSBorderBottom extends CSSSubFieldProperty {
  function getPropertyCode() {
    return CSS_BORDER_BOTTOM;
  }

  function getPropertyName() {
    return 'border-bottom';
  }

  function parse($value) {
    if ($value == 'inherit') {
      return CSS_PROPERTY_INHERIT;
    };

    $border = CSSBorder::parse($value);
    return $border->bottom;
  }
}

?>