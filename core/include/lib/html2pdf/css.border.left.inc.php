<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.border.left.inc.php,v 1.2 2008-06-08 18:29:01 cvs Exp $

class CSSBorderLeft extends CSSSubFieldProperty {
  function getPropertyCode() {
    return CSS_BORDER_LEFT;
  }

  function getPropertyName() {
    return 'border-left';
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