<?php

// Prevent conflicts with other class loaders
class ClassMapAutoloader1401820303 {
	private static $instance;
	public static $list;

	protected function __construct() {
		spl_autoload_register(array($this, "loadClass"));
		self::$list = array(
			"aggregate" => "../vendor/ddd/Aggregate.php",
			"aggregateroot" => "../vendor/ddd/AggregateRoot.php",
			"createevent" => "../vendor/ddd/CreateEvent.php",
			"deleteevent" => "../vendor/ddd/DeleteEvent.php",
			"domainevent" => "../vendor/ddd/DomainEvent.php",
			"domaineventpublisher" => "../vendor/ddd/DomainEventPublisher.php",
			"domaineventservice" => "../vendor/ddd/DomainEventService.php",
			"domaineventsubscriber" => "../vendor/ddd/DomainEventSubscriber.php",
			"domainobject" => "../vendor/ddd/DomainObject.php",
			"domainservice" => "../vendor/ddd/DomainService.php",
			"entity" => "../vendor/ddd/Entity.php",
			"factory" => "../vendor/ddd/Factory.php",
			"nullentity" => "../vendor/ddd/NullEntity.php",
			"nullvalueobject" => "../vendor/ddd/NullValueObject.php",
			"projection" => "../vendor/ddd/Projection.php",
			"queryobject" => "../vendor/ddd/QueryObject.php",
			"identitymap" => "../vendor/ddd/repository/IdentityMap.php",
			"objectmapper" => "../vendor/ddd/repository/ObjectMapper.php",
			"querymap" => "../vendor/ddd/repository/QueryMap.php",
			"repository" => "../vendor/ddd/repository/Repository.php",
			"row" => "../vendor/ddd/repository/row/Row.php",
			"rows" => "../vendor/ddd/repository/row/Rows.php",
			"rowmapper" => "../vendor/ddd/repository/RowMapper.php",
			"tableinformation" => "../vendor/ddd/repository/TableInformation.php",
			"valueobject" => "../vendor/ddd/ValueObject.php"
		);
	}

	public static function initialize() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function loadClass($className) {
		$className = strtolower($className);
		if (isset(self::$list[$className])) {
			require_once self::$list[$className];
		}
	}
}

// Make sure this 'singleton' is initialized
// It prevents executing an if statement on a getInstance singleton method :-)
ClassMapAutoloader1401820303::initialize();