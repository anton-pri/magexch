<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.border.left.color.inc.php,v 1.2 2008-06-08 18:29:01 cvs Exp $

class CSSBorderLeftColor extends CSSSubProperty {
  function CSSBorderLeftColor(&$owner) {
    $this->CSSSubProperty($owner);
  }

  function setValue(&$owner_value, &$value) {
    $owner_value->left->setColor($value);
  }

  function getValue(&$owner_value) {
    return $owner_value->left->color->copy();
  }

  function getPropertyCode() {
    return CSS_BORDER_LEFT_COLOR;
  }

  function getPropertyName() {
    return 'border-left-color';
  }

  function parse($value) {
    if ($value == 'inherit') {
      return CSS_PROPERTY_INHERIT;
    }

    return parse_color_declaration($value);
  }
}

?>