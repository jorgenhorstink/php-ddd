<?php

abstract class AggregateRoot extends Entity {

    protected static function publishEvent (DomainEvent $event) {
        DomainEventService::getInstance()->publish($event);
    }
}