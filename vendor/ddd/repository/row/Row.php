<?php

class Row {

	private $row;

	public function __construct ($row) {
		$this->row = $row;
	}

	public static function create ($row) {
		return new Row($row);
	}

	public function bool ($field) {
		return (int) $this->row[$field] === 1 ? true : false;
	}

	public function boolOrNull ($field) {
		return $this->row[$field] !== null ? $this->bool($field) : null;
	}

	public function int ($field) {
		return (int) $this->row[$field];
	}

	public function intOrNull ($field) {
		return $this->row[$field] !== null ? $this->int($field) : null;
	}

	public function string ($field) {
		return (string) $this->row[$field];
	}

	public function stringOrNull ($field) {
		return $this->row[$field] !== null ? $this->string($field) : null;
	}

	public function id ($field) {
		return $this->int($field);
	}

	public function idOrNull ($field) {
		return $this->row[$field] !== null ? $this->id($field) : null;
	}
	
	public function date ($field) {
		return Date::fromDateTime(DateTime::createFromFormat('Y-m-d', substr($this->row[$field], 0, 10)));
	}
	
	public function dateOrNull ($field) {
		return $this->row[$field] !== null ? $this->date($field) : null;
	}
	
	public function timePoint ($field) {
		return TimePoint::fromDateTime(DateTime::createFromFormat('Y-m-d H:i:s', $this->row[$field]));
	}
	
	public function timePointOrNull ($field) {
		return $this->row[$field] !== null ? $this->timePoint($field) : null;
	}

    public function time ($field) {
        return Time::fromDateTime(DateTime::createFromFormat('H:i:s', $this->row[$field]));
    }

    public function timeOrNull ($field) {
        return $this->row[$field] !== null ? $this->time($field) : null;
    }

	public function dateTime ($field) {
		return DateTime::createFromFormat(MYSQL_DATETIME, $this->row[$field]);
	}

	public function dateTimeOrNull ($field) {
		return $this->row[$field] !== null ? $this->dateTime($field) : null;
	}

	public function bedrag ($field) {
		return Bedrag::createFromInteger($this->row[$field]);
	}

	public function bedragOrNull ($field) {
		return $this->row[$field] !== null ? $this->bedrag($field) : null;
	}
}

?>