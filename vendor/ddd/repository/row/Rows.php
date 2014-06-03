<?php

class Rows extends ArrayList {

	public function __construct ($rows) {
        foreach ($rows as $row) {
            $this->list[] = new Row($row);
        }
	}
}