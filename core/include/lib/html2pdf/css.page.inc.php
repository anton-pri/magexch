<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.page.inc.php,v 1.2 2008-06-08 18:29:02 cvs Exp $

class CSSPage extends CSSPropertyHandler {
  function CSSPage() { 
    $this->CSSPropertyHandler(true, true); 
  }

  function default_value() { 
    return 'auto'; 
  }

  function parse($value) {
    return $value;
  }

  function getPropertyCode() {
    return CSS_PAGE;
  }

  function getPropertyName() {
    return 'page';
  }
}

CSS::register_css_property(new CSSPage());

?>