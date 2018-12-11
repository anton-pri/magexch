<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.direction.inc.php,v 1.2 2008-06-08 18:29:02 cvs Exp $

define('DIRECTION_LTR', 1);
define('DIRECTION_RTF', 1);

class CSSDirection extends CSSPropertyStringSet {
  function CSSDirection() { 
    $this->CSSPropertyStringSet(true, 
                                true,
                                array('lrt' => DIRECTION_LTR,
                                      'rtl' => DIRECTION_RTF)); 
  }

  function default_value() { 
    return DIRECTION_LTR; 
  }

  function getPropertyCode() {
    return CSS_DIRECTION;
  }

  function getPropertyName() {
    return 'direction';
  }
}

CSS::register_css_property(new CSSDirection);

?>