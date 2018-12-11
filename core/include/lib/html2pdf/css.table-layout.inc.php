<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.table-layout.inc.php,v 1.2 2008-06-08 18:29:03 cvs Exp $

define('TABLE_LAYOUT_AUTO',   1);
define('TABLE_LAYOUT_FIXED',  2);

class CSSTableLayout extends CSSPropertyStringSet {
  function CSSTableLayout() { 
    $this->CSSPropertyStringSet(false, 
                                false,
                                array('auto'  => TABLE_LAYOUT_AUTO,
                                      'fixed' => TABLE_LAYOUT_FIXED)); 
  }

  function default_value() { 
    return TABLE_LAYOUT_AUTO; 
  }

  function getPropertyCode() {
    return CSS_TABLE_LAYOUT;
  }

  function getPropertyName() {
    return 'table-layout';
  }
}

CSS::register_css_property(new CSSTableLayout());
  
?>