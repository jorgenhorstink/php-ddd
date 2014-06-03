<?php

abstract class Entity extends DomainObject {

	protected $id;

	public function __construct($id) {
		$this->id = $this->intOrNull($id);
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	public function hasId() {
		return $this->id !== null;
	}
}

?>