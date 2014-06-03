<?php

abstract class DomainObject {

	protected function stringOrNull($value) {
		return $value !== null ? (string) $value : null;
	}

	protected function intOrNull($value) {
		return $value !== null ? (int) $value : null;
	}
}

?>