<?php

class DomainEventService extends DomainService implements \DomainEventPublisher {
	
	protected static $instance;
	private $subscribers;
	
	protected function __construct() {
		$this->subscribers = new \ArrayList();
	}
	
	public static function initialize () {
		static::$instance = new static();
	}

	/**
	 * @return static
	 */
	public static function getInstance () {
		return static::$instance;
	}
	
	public function publish (\DomainEvent $domainEvent) {
		foreach ($this->subscribers as $subscriber) {
			$method = 'process' . ucfirst(get_class($domainEvent));

			if (method_exists($subscriber, $method)) {
				$subscriber->$method($domainEvent);
			} else {
				$subscriber->processDomainEvent($domainEvent);
			}
		}
	}
	
	public function subscribe (\DomainEventSubscriber $subscriber) {
		if (!$this->hasSubscriber ($subscriber)){
			$this->subscribers->add($subscriber);
		}
	}

	public function hasSubscriber ($subscriber) {
		$this->subscribers->contains($subscriber);
	}
	
	public function hasSubscribers () {
		return $this->subscribers !== null;
	}
	
	
	
}
	

?>