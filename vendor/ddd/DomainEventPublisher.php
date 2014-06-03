<?php

interface DomainEventPublisher {
	
	public function publish (DomainEvent $domainEvent);
	
	public function subscribe (DomainEventSubscriber $subscriber);

}

?>