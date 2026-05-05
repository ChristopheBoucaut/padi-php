<?php

declare(strict_types = 1);

namespace Padi\Tests\DomainEvent;

use Padi\DomainEvent\DomainEventRecorderInterface;
use Padi\DomainEvent\DomainEventRecorderTrait;

class DomainEventRecorderSample implements DomainEventRecorderInterface
{
    use DomainEventRecorderTrait;

    public function doSomething(): void
    {
        $this->recordDomainEvent(static::generateEvent1());
    }

    public function doSomethingElse(): void
    {
        $this->recordDomainEvent(static::generateEvent2());
    }

    public static function generateEvent1(): object
    {
        return (object) ["data" => "value"];
    }

    public static function generateEvent2(): object
    {
        return (object) ["data" => "value2"];
    }
}
