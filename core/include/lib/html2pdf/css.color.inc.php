<?php
class CSSColor extends CSSPropertyHandler {
  function CSSColor() { 
    $this->CSSPropertyHandler(true, true); 
  }

  function default_value() { 
    return new Color(array(0,0,0),false); 
  }

  function parse($value) {
    if ($value === 'inherit') {
      return CSS_PROPERTY_INHERIT;
    };

    return parse_color_declaration($value);
  }

  function getPropertyCode() {
    return CSS_COLOR;
  }

  function getPropertyName() {
    return 'color';
  }
}

CSS::register_css_property(new CSSColor);

?>
