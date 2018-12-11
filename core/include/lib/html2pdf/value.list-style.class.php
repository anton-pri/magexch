<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/value.list-style.class.php,v 1.2 2008-06-08 18:29:06 cvs Exp $

require_once(HTML2PS_DIR.'value.generic.php');

class ListStyleValue extends CSSValue {
  var $image;
  var $position;
  var $type;

  function doInherit(&$state) {
    if ($this->image === CSS_PROPERTY_INHERIT) {
      $value = $state->getInheritedProperty(CSS_LIST_STYLE_IMAGE);
      $this->image = $value->copy();
    };

    if ($this->position === CSS_PROPERTY_INHERIT) {
      $value = $state->getInheritedProperty(CSS_LIST_STYLE_POSITION);
      $this->position = $value;
    };

    if ($this->type === CSS_PROPERTY_INHERIT) {
      $value = $state->getInheritedProperty(CSS_LIST_STYLE_TYPE);
      $this->type = $value;
    };
  }

  function is_default() {
    return 
      $this->image->is_default() &&
      $this->position == CSSListStylePosition::default_value() &&
      $this->type     == CSSListStyleType::default_value();
  }

  function &copy() {
    $object =& new ListStyleValue;

    if ($this->image === CSS_PROPERTY_INHERIT) {
      $object->image = CSS_PROPERTY_INHERIT;
    } else {
      $object->image = $this->image->copy();
    };

    $object->position = $this->position;
    $object->type     = $this->type;

    return $object;
  }
}

?>