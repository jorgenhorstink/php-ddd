<?php

abstract class Repository {

    private $database;
    private $tableName;
    private $rowMapper;
    private $identityMap;
    private $queryMap;

    /**
     *
     * @param Database $database
     */
    public static function initialize (Database $database) {
        static::$instance = new static($database);
    }

    /**
     * @return static
     */
    public static function getInstance () {
        return static::$instance;
    }

    protected function __construct (Database $database, $tableName, RowMapper $rowMapper) {
        $this->database = $database;
        $this->tableName = (string) $tableName;
        $this->rowMapper = $rowMapper;
        $this->identityMap = new IdentityMap();
        $this->queryMap = new QueryMap();
    }

    /**
     * @return ArrayList
     */
    protected function getEmptyList () {
        return new ArrayList();
    }

    protected function selectQuery () {
        $tableName = $this->tableName;
        return "SELECT * FROM `$tableName`";
    }

    protected function byId ($id = null, $query = null) {
        if ($id === null) {
            return null;
        }

        return $this->identityMap->containsKey($id) ? $this->identityMap->get($id) : $this->getOne($query !== null ? $query : "WHERE `{$this->tableName}_id` = ?", new ArrayList($id));
    }

    private function modifiedQuery ($query, $modifier = null) {
        return $modifier === null ? $query : "$query $modifier";
    }

    protected function getList ($modifier = null, $parameters = null) {
        if (!$parameters instanceof AbstractList) {
            $parameters = ArrayList::fromArray(func_get_args(), 1);
        }

        $resultSet = $this->execute($this->modifiedQuery($this->selectQuery(), $modifier), $parameters);
        $tableName = $this->tableName;



        $objects = $this->getEmptyList();

        if ($resultSet->length() > 0) {
            //$rows = array();
            $idField = "{$tableName}_id";
            $firstRow = $resultSet->first();
            //$currentId = $firstRow[$idField];

			$map = array();
			foreach ($resultSet as $row) {
				$aggregateId = $row[$idField];
				if (!isset($map[$aggregateId])) {
					$map[$aggregateId] = array($row);
				} else {
					$current = $map[$aggregateId];
					$current[] = $row;
					$map[$aggregateId] = $current;
				}
			}

			foreach ($map as $aggregateId => $rows) {
				if ($this->identityMap->containsKey($aggregateId)) {
					$object = $this->identityMap->get($aggregateId);
				} else {
					$object = $this->rowMapper->map(new Rows($rows));
					$this->identityMap->put($object);
				}
				$objects->add($object);
				unset($map[$aggregateId]);
			}
			/*

            foreach ($resultSet as $row) {
                $aggregateId = $row[$idField];
                if ($aggregateId === $currentId) {
                    $rows[] = $row;
                } else {
                    if ($this->identityMap->containsKey($currentId)) {
                        $object = $this->identityMap->get($currentId);
                    } else {
                        $object = $this->rowMapper->map(new Rows($rows));
                        $this->identityMap->put($object);
                    }
                    $objects->add($object);

                    $rows = array($row);
                    $currentId = $aggregateId;
                }
            }

            if ($this->identityMap->containsKey($currentId)) {
                $object = $this->identityMap->get($currentId);
            } else {
                $object = $this->rowMapper->map(new Rows($rows));
                $this->identityMap->put($object);
            }
            $objects->add($object);
			*/
        }

        return $objects;
    }

    protected function getOne ($modifier = null, $parameters = null) {
        if (!$parameters instanceof AbstractList) {
            $parameters = ArrayList::fromArray(func_get_args(), 1);
        }

        return $this->getList($modifier, $parameters)->first();
    }

    protected function exists ($modifier = null, $parameters = null) {
        if (!$parameters instanceof AbstractList) {
            $parameters = ArrayList::fromArray(func_get_args(), 1);
        }

        return $this->execute($this->modifiedQuery($this->selectQuery(), $modifier), $parameters)->first() !== null;
    }

    protected function count ($modifier = null, $parameters = null) {
        if (!$parameters instanceof AbstractList) {
            $parameters = ArrayList::fromArray(func_get_args(), 1);
        }

        $row = $this->execute($this->modifiedQuery("SELECT COUNT(1) AS `count` FROM `{$this->tableName}`", $modifier), $parameters)->first();
        return (int) $row['count'];
    }

    protected function saveAggregateRoot (AggregateRoot $aggregateRoot, ArrayMap $map) {

        if (!$aggregateRoot->hasId()) {
            $this->saveObject($map, $this->tableName);

            $aggregateRoot->setId($this->database->lastInsertId());
            $this->identityMap->put($aggregateRoot);
        } else {
            $this->changeAggregateRoot($aggregateRoot, $map);
        }
    }

    protected function saveEntity (ArrayMap $map, $tableName, Entity $entity) {
        $this->saveObject($map, $tableName);
        $entity->setId($this->database->lastInsertId());
    }

    protected function saveObject (ArrayMap $map, $tableName) {
        $this->saveObjects(new ArrayList($map), $tableName);
    }

    protected function saveObjects (AbstractList $maps, $tableName) {
        if ($maps->isEmpty()) {
            return;
        }
        $keys = ($maps->first()->keys()->join(', '));

        $objectList = new ArrayList();
        foreach ($maps as $map) {
            $valueComponentList = new ArrayList();
            foreach ($map as $value) {
                $valueComponentList->add($this->parsedValue($value));
            }
            $objectList->add('(' . $valueComponentList->join(',') . ')');
        }
        $values = $objectList->join(',');

        $sql = "INSERT INTO `$tableName` ($keys) VALUES $values";

        $this->database->query($sql);

        $this->queryMap->reset();
    }

    private function changeAggregateRoot (AggregateRoot $aggregateRoot, ArrayMap $map) {
        $parsedMap = new ArrayMap();
        foreach ($map as $key => $value) {
            $parsedMap->put($key, $this->parsedValue($value));
        }

        $assignments = $parsedMap->map(function ($key, $value) { return "$key = $value"; })->values()->join(', ');

        $tableName = $this->tableName;
        $aggregateRootId = (int) $aggregateRoot->getId();
        $sql = "UPDATE `$tableName` SET $assignments WHERE `{$tableName}_id` = $aggregateRootId";

        $this->database->query($sql);

        $this->queryMap->reset();
    }

    protected function deleteAggregate (AggregateRoot $object) {
        $tableName = $this->tableName;
        $this->execute("DELETE FROM `$tableName` WHERE `{$tableName}_id` = ?", $object->getId());

        $this->queryMap->reset();
    }

    protected function query($sql) {
        return $this->database->query($sql);
    }

    protected function putInQueryMap ($query, AbstractList $list) {

        $this->queryMap->put($query, $list);
    }

    protected function execute ($query, $parameters = null) {
		if (!$parameters instanceof AbstractList) {
            $parameters = ArrayList::fromArray(func_get_args(), 1);
		}

		$parsedQuery = $this->parsedQuery($query, $parameters);

        if ($this->queryMap->containsQuery($parsedQuery)) {
            $resultSet = $this->queryMap->get($parsedQuery);
        } else {
            $resultSet = $this->database->query($parsedQuery);
            $this->queryMap->put($parsedQuery, $resultSet);
        }

        return $resultSet;
    }

    protected function parsedQuery ($sql, $parameters = null) {
        if (!$parameters instanceof AbstractList) {
            $parameters = ArrayList::fromArray(func_get_args(), 1);
        }

        $quote = null;
        $character = null;
        $parameterIndex = 0;

        for ($i = 0; $i < strlen($sql); $i++) {
            $character = $sql[$i];

            // Ignore the escape character and the character following the escape character
            if ($character === '\\') {
                $i++;
                continue;
            }

            // If we are in a quotation, ignore all characters and close the quotation at the closing sign
            if ($quote !== null) {
                if ($character === $quote) {
                    $quote = null;
                }
                continue;
            }

            // If beginning quotes are found, start a quotation
            if ($character === "'" || $character === '"') {
                $quote = $character;
            }

            // If we find a question mark, replace it
            if ($character === '?') {
                $sql = substr_replace($sql, $this->parsedValue($parameters->get($parameterIndex++)), $i, 1);
                // When we replace a question mark, jump back to inspect the first character in the replaced string
                // This is important in case the first character opens a quotation
                $i--;
            }
        }

        return $sql;
    }

    protected function parsedValue ($value) {
        if ($value instanceof Enumeration) {
            $value = $value->value();
        }

        if ($value instanceof String) {
            $value = $value->toString();
        } else if ($value instanceof Date) {
            $value = $value->format(Date::ISO);
        } else if ($value instanceof TimePoint) {
            $value = $value->format(TimePoint::ISO);
        } else if ($value instanceof Bedrag) {
            $value = $value->toInteger();
        }

        switch (gettype($value)) {
            case 'NULL':
                return 'NULL';
            // In MySQL, 1 and 0 are synonyms for TRUE and FALSE respectively
            case 'boolean':
                return $value ? 1 : 0;
            case 'integer':
            case 'double':
                return $value;
            case 'string':
                return $this->database->quotedEscapeString($value);
        }

        throw new Exception('Invalid value in Repository::parsedValue(): ' . String::dump($value));
    }

    public function time (Time $value) {
        return $this->string($value->format(Time::ISO));
    }

    protected function timeOrNull (Time $value = null) {
        return $value !== null ? $this->time($value) : $this->null();
    }




}
