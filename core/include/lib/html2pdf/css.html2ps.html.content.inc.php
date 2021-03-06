<?php
// $Header: /var/cvs/arsgate/client_cpanel/include/html2pdf/css.html2ps.html.content.inc.php,v 1.2 2008-06-08 18:29:02 cvs Exp $

require_once(HTML2PS_DIR.'value.content.php');

class CSSHTML2PSHTMLContent extends CSSPropertyHandler {
  function CSSHTML2PSHTMLContent() { 
    $this->CSSPropertyHandler(false, false); 
  }

  function &default_value() { 
    $data =& new ValueContent();
    return $data;
  }

  // CSS 2.1 p 12.2: 
  // Value: [ <string> | <uri> | <counter> | attr(X) | open-quote | close-quote | no-open-quote | no-close-quote ]+ | inherit
  //
  // TODO: process values other than <string>
  //
  function &parse($value) {
    if ($value === 'inherit') {
      return CSS_PROPERTY_INHERIT;
    };

    $value_obj =& ValueContent::parse($value);
    return $value_obj;
  }

  function getPropertyCode() {
    return CSS_HTML2PS_HTML_CONTENT;
  }

  function getPropertyName() {
    return '-html2ps-html-content';
  }
}

CSS::register_css_property(new CSSHTML2PSHTMLContent);

?>