<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.pseudo.cellspacing.inc.php,v 1.2 2008-06-08 18:29:03 cvs Exp $

class CSSCellSpacing extends CSSPropertyHandler {
  function CSSCellSpacing() { 
    $this->CSSPropertyHandler(true, false); 
  }

  function default_value() { 
    return Value::fromData(1, UNIT_PX);
  }

  function parse($value) { 
    return Value::fromString($value);
  }

  function getPropertyCode() {
    return CSS_HTML2PS_CELLSPACING;
  }

  function getPropertyName() {
    return '-html2ps-cellspacing';
  }
}

CSS::register_css_property(new CSSCellSpacing);

?>