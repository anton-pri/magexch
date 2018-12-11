<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.border.bottom.width.inc.php,v 1.2 2008-06-08 18:29:01 cvs Exp $

class CSSBorderBottomWidth extends CSSSubProperty {
  function CSSBorderBottomWidth(&$owner) {
    $this->CSSSubProperty($owner);
  }

  function setValue(&$owner_value, &$value) {
    if ($value != CSS_PROPERTY_INHERIT) {
      $owner_value->bottom->width = $value->copy();
    } else {
      $owner_value->bottom->width = $value;
    };
  }

  function getValue(&$owner_value) {
    return $owner_value->bottom->width;
  }

  function getPropertyCode() {
    return CSS_BORDER_BOTTOM_WIDTH;
  }

  function getPropertyName() {
    return 'border-bottom-width';
  }

  function parse($value) {
    if ($value == 'inherit') {
      return CSS_PROPERTY_INHERIT;
    }

    $width_handler = CSS::get_handler(CSS_BORDER_WIDTH);
    $width = $width_handler->parse_value($value);
    return $width;
  }
}

?>