<?php

class EventReturn {

	var $params = array();
	
	var $control = 0;
	
	var $result = null;


	function __construct($r=null, $p=null, $c=null) {
		$this->result=$r;
		$this->params=$p;
		$this->control=$c;
	}
	
	function getResult() {
		return $this->result;
	}
	function getReturn() {
		return $this->result;
	}
	function getParams() {
		return $this->params;
	}
	function getControl() {
		return $this->control;
	}
	
}
