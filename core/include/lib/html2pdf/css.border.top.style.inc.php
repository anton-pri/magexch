<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.border.top.style.inc.php,v 1.2 2008-06-08 18:29:01 cvs Exp $

class CSSBorderTopStyle extends CSSSubProperty {
  function CSSBorderTopStyle(&$owner) {
    $this->CSSSubProperty($owner);
  }

  function setValue(&$owner_value, &$value) {
    $owner_value->top->style = $value;
  }

  function getValue(&$owner_value) {
    return $owner_value->top->style;
  }

  function getPropertyCode() {
    return CSS_BORDER_TOP_STYLE;
  }

  function getPropertyName() {
    return 'border-top-style';
  }

  function parse($value) {
    if ($value == 'inherit') {
      return CSS_PROPERTY_INHERIT;
    }

    return CSSBorderStyle::parse_style($value);
  }
}

?>