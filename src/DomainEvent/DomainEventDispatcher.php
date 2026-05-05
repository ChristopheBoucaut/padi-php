<?php

declare(strict_types = 1);

namespace Padi\DomainEvent;

use Psr\EventDispatcher\EventDispatcherInterface;

class DomainEventDispatcher
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function dispatch(DomainEventRecorderInterface ...$entities): void
    {
        foreach ($entities as $entity) {
            foreach ($entity->pullDomainEvents() as $domainEvents) {
                $this->dispatcher->dispatch($domainEvents);
            }
        }
    }
}
