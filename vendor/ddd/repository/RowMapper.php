<?php

abstract class RowMapper {

	public static function create () {
		return new static();
	}

	public abstract function map (Rows $rows);
}

?>