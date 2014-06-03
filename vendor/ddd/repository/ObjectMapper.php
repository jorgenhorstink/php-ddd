<?php

abstract class ObjectMapper {

	private $database;

	private function __construct (Database $database) {
		$this->database = $database;
	}

	public static function create () {
		return new static(DatabasePool::getInstance()->getDatabase());
	}

	protected function null () {
		return 'NULL';
	}

	protected function bool ($value) {
		return $value === true ? 1 : 0;
	}

	protected function boolOrNull ($value) {
		return $value !== null ? $this->bool($value) : $this->null();
	}

	protected function int ($value) {
		return (int) $value;
	}

	protected function intOrNull ($value) {
		return $value !== null ? $this->int($value) : $this->null();
	}

	protected function unquotedString ($value) {
		return $this->database->escapeString($value);
	}

	protected function string ($value) {
		return $this->database->quotedEscapeString($value);
	}

	protected function stringOrNull ($value) {
		return $value !== null ? $this->string($value) : $this->null();
	}

	protected function id ($value) {
		return $this->int($value);
	}

	protected function idOrNull ($value) {
		return $value !== null ? $this->id($value) : $this->null();
	}
}

?>