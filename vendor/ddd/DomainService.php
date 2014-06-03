<?php

abstract class DomainService extends DomainObject {
	
	private function __construct(){}
	
	public static function getInstance () {
		if (static::$instance === null) {
			static::$instance = new static();
		}
		return static::$instance;
	}
	
} 


?>