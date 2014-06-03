<?php

interface DomainEventSubscriber extends EventSubscriber {

	public function processDomainEvent (DomainEvent $domainEvent);
}