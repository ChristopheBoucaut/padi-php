<?php

declare(strict_types = 1);

namespace Padi\Tests\DomainEvent;

use PHPUnit\Framework\TestCase;

class DomainEventRecorderTraitTest extends TestCase
{
    public function testPullDomainEvents(): void
    {
        $entity = new DomainEventRecorderSample();
        static::assertSame([], $entity->pullDomainEvents());

        $entity->doSomething();
        static::assertEquals([DomainEventRecorderSample::generateEvent1()], $entity->pullDomainEvents());
        static::assertSame([], $entity->pullDomainEvents());

        $entity->doSomething();
        $entity->doSomethingElse();
        $entity->doSomething();
        static::assertEquals(
            [
                DomainEventRecorderSample::generateEvent1(),
                DomainEventRecorderSample::generateEvent2(),
                DomainEventRecorderSample::generateEvent1(),
            ],
            $entity->pullDomainEvents(),
        );
        static::assertSame([], $entity->pullDomainEvents());
    }
}
