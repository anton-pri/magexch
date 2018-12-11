<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.border.left.width.inc.php,v 1.2 2008-06-08 18:29:01 cvs Exp $

class CSSBorderLeftWidth extends CSSSubProperty {
  function CSSBorderLeftWidth(&$owner) {
    $this->CSSSubProperty($owner);
  }

  function setValue(&$owner_value, &$value) {
    if ($value != CSS_PROPERTY_INHERIT) {
      $owner_value->left->width = $value->copy();
    } else {
      $owner_value->left->width = $value;
    };
  }

  function getValue(&$owner_value) {
    return $owner_value->left->width;
  }

  function getPropertyCode() {
    return CSS_BORDER_LEFT_WIDTH;
  }

  function getPropertyName() {
    return 'border-left-width';
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