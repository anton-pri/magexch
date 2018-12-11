<?php
class CSSDisplay extends CSSPropertyHandler {
  function CSSDisplay() { $this->CSSPropertyHandler(false, false); }

  function get_parent() { 
    if (isset($this->_stack[1]))
      return $this->_stack[1][0]; 
    else
      return 'block';
  }

  function default_value() { return "inline"; }

  function getPropertyCode() {
    return CSS_DISPLAY;
  }

  function getPropertyName() {
    return 'display';
  }

  function parse($value) { 
    return trim(strtolower($value));
  }
}

CSS::register_css_property(new CSSDisplay);

function is_inline_element($display) {
  return 
    $display == "inline" ||
    $display == "inline-table" ||
    $display == "compact" ||
    $display == "run-in" || 
    $display == "-button" ||
    $display == "-checkbox" ||
    $display == "-iframe" ||
    $display == "-image" ||
    $display == "inline-block" ||
    $display == "-radio" ||
    $display == "-select";
}
?>
