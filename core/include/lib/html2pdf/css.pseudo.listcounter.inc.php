<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.pseudo.listcounter.inc.php,v 1.2 2008-06-08 18:29:03 cvs Exp $

class CSSPseudoListCounter extends CSSPropertyHandler {
  function CSSPseudoListCounter() { 
    $this->CSSPropertyHandler(true, false); 
  }

  function default_value() { 
    return 0; 
  }

  function getPropertyCode() {
    return CSS_HTML2PS_LIST_COUNTER;
  }

  function getPropertyName() {
    return '-html2ps-list-counter';
  }

  function parse($value) {
    return (int)$value;
  }
}

CSS::register_css_property(new CSSPseudoListCounter);

?>