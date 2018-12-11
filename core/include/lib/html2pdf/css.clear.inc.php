<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.clear.inc.php,v 1.2 2008-06-08 18:29:01 cvs Exp $

define('CLEAR_NONE',0);
define('CLEAR_LEFT',1);
define('CLEAR_RIGHT',2);
define('CLEAR_BOTH',3);

class CSSClear extends CSSPropertyStringSet {
  function CSSClear() { 
    $this->CSSPropertyStringSet(false, 
                                false,
                                array('inherit' => CSS_PROPERTY_INHERIT,
                                      'left'    => CLEAR_LEFT,
                                      'right'   => CLEAR_RIGHT,
                                      'both'    => CLEAR_BOTH,
                                      'none'    => CLEAR_NONE)); 
  }

  function default_value() { 
    return CLEAR_NONE; 
  }

  function getPropertyCode() {
    return CSS_CLEAR;
  }

  function getPropertyName() {
    return 'clear';
  }
}

CSS::register_css_property(new CSSClear);

?>