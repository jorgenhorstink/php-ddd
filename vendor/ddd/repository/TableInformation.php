<?php

class TableInformation extends ValueObject {
	
	private $tableName;
	private $primaryKey;
	private $namespace;
	
	public function __construct($tableName, $primaryKey = null, $namespace = null) {
		$this->tableName = $tableName;
		$this->primaryKey = $primaryKey !== null ? $primaryKey : $tableName . '_id';
		$this->namespace = $namespace;
	}
	
	public function getTableName () {
		return $this->tableName;
	}
	
	public function getNamespace () {
		return $this->namespace;
	}
	
	public function getPrimaryKey () {
		return $this->primaryKey;
	}
	
	public function hasNonDefaultNamespace () {
		return $this->namespace === null;
	}
	
	
}



?>