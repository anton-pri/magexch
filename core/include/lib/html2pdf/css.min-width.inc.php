<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.min-width.inc.php,v 1.2 2008-06-08 18:29:02 cvs Exp $

class CSSMinWidth extends CSSSubFieldProperty {
  function CSSMinWidth(&$owner, $field) {
    $this->CSSSubFieldProperty($owner, $field);
  }

  function getPropertyCode() {
    return CSS_MIN_WIDTH;
  }

  function getPropertyName() {
    return 'min-width';
  }

  function parse($value) {
    if ($value == 'inherit') {
      return CSS_PROPERTY_INHERIT;
    }
    
    return Value::fromString($value);
  }
}

?>