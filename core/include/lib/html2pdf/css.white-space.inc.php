<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.white-space.inc.php,v 1.2 2008-06-08 18:29:03 cvs Exp $

define('WHITESPACE_NORMAL',   0);
define('WHITESPACE_PRE',      1);
define('WHITESPACE_NOWRAP',   2);
define('WHITESPACE_PRE_WRAP', 3);
define('WHITESPACE_PRE_LINE', 4);

class CSSWhiteSpace extends CSSPropertyStringSet {
  function CSSWhiteSpace() { 
    $this->CSSPropertyStringSet(true, 
                                true,
                                array('normal'   => WHITESPACE_NORMAL,
                                      'pre'      => WHITESPACE_PRE,
                                      'pre-wrap' => WHITESPACE_PRE_WRAP,
                                      'nowrap'   => WHITESPACE_NOWRAP,
                                      'pre-line' => WHITESPACE_PRE_LINE)); 
  }

  function default_value() { 
    return WHITESPACE_NORMAL; 
  }

  function getPropertyCode() {
    return CSS_WHITE_SPACE;
  }

  function getPropertyName() {
    return 'white-space';
  }
}

CSS::register_css_property(new CSSWhiteSpace);
  
?>