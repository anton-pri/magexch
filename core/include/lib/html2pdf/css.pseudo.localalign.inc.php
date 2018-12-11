<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.pseudo.localalign.inc.php,v 1.2 2008-06-08 18:29:03 cvs Exp $

define('LA_LEFT',0);
define('LA_CENTER',1);
define('LA_RIGHT',2);

class CSSLocalAlign extends CSSPropertyHandler {
  function CSSLocalAlign() { $this->CSSPropertyHandler(false, false); }

  function default_value() { return LA_LEFT; }

  function parse($value) { return $value; }

  function getPropertyCode() {
    return CSS_HTML2PS_LOCALALIGN;
  }

  function getPropertyName() {
    return '-html2ps-localalign';
  }
}

CSS::register_css_property(new CSSLocalAlign);

?>