<?php

class QueryMap {

	private $map = array();

	public function containsQuery ($query) {
		return isset($this->map[$query]);
	}

	public function get ($query) {
		return $this->containsQuery($query) ? $this->map[$query] : null;
	}

	public function put ($query, ResultSet $resultSet) {
		$this->map[$query] = $resultSet;
	}

    public function reset () {
        $this->map = array();
    }
}