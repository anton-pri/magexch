<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.word-spacing.inc.php,v 1.2 2008-06-08 18:29:03 cvs Exp $

class CSSWordSpacing extends CSSPropertyHandler {
  var $_default_value;

  function CSSWordSpacing() { 
    $this->CSSPropertyHandler(false, true); 

    $this->_default_value = Value::fromString("0");
  }

  function default_value() { 
    return $this->_default_value;
  }

  function parse($value) {
    $value = trim($value);

    if ($value === 'inherit') {
      return CSS_PROPERTY_INHERIT;
    };

    if ($value === 'normal') { 
      return $this->_default_value; 
    };

    return Value::fromString($value);
  }

  function getPropertyCode() {
    return CSS_WORD_SPACING;
  }

  function getPropertyName() {
    return 'word-spacing';
  }
}

CSS::register_css_property(new CSSWordSpacing);

?>
