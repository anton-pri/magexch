<?php
# kornev, again - small changes

class DOMTree {
  var $domelement;
  var $content;
  
  function DOMTree($domelement) {
    $this->domelement = $domelement;
    $this->content = $domelement->textContent;
  }

  function &document_element() { 
    return $this; 
  }

  function &first_child() {
    if ($this->domelement->firstChild) {
      $child =& new DOMTree($this->domelement->firstChild);
      return $child;
    } 
    return false;
  }

  function &from_DOMDocument($domdocument) { 
    $tree =& new DOMTree($domdocument->documentElement); 
    return $tree;
  }

  function get_attribute($name) { 
    return $this->domelement->getAttribute($name); 
  }

  function get_content() { 
    return $this->domelement->textContent; 
  }

  function has_attribute($name) { 
    return $this->domelement->hasAttribute($name); 
  }

  function &last_child() {
    $child =& $this->first_child();

    if ($child) {
      $sibling =& $child->next_sibling();
      while ($sibling) {
        $child =& $sibling;
        $sibling =& $child->next_sibling();
      };
    };

    return $child;
  }

  function &next_sibling() {
    if ($this->domelement->nextSibling) {
      $child =& new DOMTree($this->domelement->nextSibling);
      return $child;
    }
    return false;
  }
  
  function node_type() { 
    return $this->domelement->nodeType; 
  }

  function &parent() {
    if ($this->domelement->parentNode) {
      $parent =& new DOMTree($this->domelement->parentNode);
      return $parent;
    }
    return false;
  }

  function &previous_sibling() {
    if ($this->domelement->previousSibling) {
      $sibling =& new DOMTree($this->domelement->previousSibling);
      return $sibling;
    }
    return false;
  }

  function tagname() { 
    return $this->domelement->localName; 
  }
}
?>
