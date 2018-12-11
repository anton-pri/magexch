<?php
require_once(HTML2PS_DIR.'value.text-indent.class.php');

class CSSTextIndent extends CSSPropertyHandler {
  function CSSTextIndent() { 
    $this->CSSPropertyHandler(true, true); 
  }

  function default_value() { 
    static $default_value;
    if (!$default_value) $default_value = new TextIndentValuePDF(array(0,false));
    return $default_value;
  }

  function parse($value) {
    if ($value === 'inherit')
      return CSS_PROPERTY_INHERIT;

    if (is_percentage($value)) { 
      return new TextIndentValuePDF(array((int)$value, true));
    } else {
      return new TextIndentValuePDF(array($value, false));
    };
  }

  function getPropertyCode() {
    return CSS_TEXT_INDENT;
  }

  function getPropertyName() {
    return 'text-indent';
  }
}

CSS::register_css_property(new CSSTextIndent());

?>
