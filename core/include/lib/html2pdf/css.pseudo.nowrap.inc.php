<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.pseudo.nowrap.inc.php,v 1.2 2008-06-08 18:29:03 cvs Exp $

define('NOWRAP_NORMAL',0);
define('NOWRAP_NOWRAP',1);

class CSSPseudoNoWrap extends CSSPropertyHandler {
  function CSSPseudoNoWrap() { $this->CSSPropertyHandler(false, false); }
  function default_value() { return NOWRAP_NORMAL; }

  function getPropertyCode() {
    return CSS_HTML2PS_NOWRAP;
  }

  function getPropertyName() {
    return '-html2ps-nowrap';
  }
}

CSS::register_css_property(new CSSPseudoNoWrap);
  
?>