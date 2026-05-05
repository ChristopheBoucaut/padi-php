<?php

declare(strict_types = 1);

namespace Padi\DomainEvent;

interface DomainEventRecorderInterface
{
    /** @return object[] */
    public function pullDomainEvents(): array;
}
