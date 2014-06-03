<?php

abstract class Projection implements DomainEventSubscriber {
	
	public function update (QueryObject $object, DomainEvent $event) {
		$object->mutate($event);
		$this->repository->register($object);
	}
}

?>