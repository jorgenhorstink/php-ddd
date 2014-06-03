<?php

class IdentityMap {

	private $map = array();

	public function containsKey ($entityId) {
		return isset($this->map[$entityId]);
	}

	public function get ($entityId) {
		return $this->containsKey($entityId) ? $this->map[$entityId] : null;
	}

	public function put (AggregateRoot $aggregate) {
		$this->map[$aggregate->getId()] = $aggregate;
	}

	public function remove (AggregateRoot $aggregate) {
		unset($this->map[$aggregate->getId()]);
	}
}

?>