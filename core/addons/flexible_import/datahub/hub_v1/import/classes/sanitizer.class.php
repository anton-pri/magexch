<?php
class sanitizer {
	public static $generic = array("'");
	
	function __construct() {
	}
    
	public static function strip($string, $strip = array()) {
		$count = count($strip);
		$string = trim($string);
		if($count == 0) return;
		$string = str_ireplace($strip, '', $string);
		return $string;
  }    
}