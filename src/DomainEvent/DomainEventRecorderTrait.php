<?php

declare(strict_types = 1);

namespace Padi\DomainEvent;

/** Global logic to implement DomainEventRecorderInterface */
trait DomainEventRecorderTrait
{
    /** @var object[] */
    private array $domainEvents = [];

    protected function recordDomainEvent(object $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    /** @return object[] */
    public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }
}
