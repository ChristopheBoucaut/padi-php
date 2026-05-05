<?php

declare(strict_types = 1);

namespace Padi\Tests\DomainEvent;

use Padi\DomainEvent\DomainEventDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class DomainEventDispatcherTest extends TestCase
{
    private EventDispatcherInterface&MockObject $eventDispatcher;
    private DomainEventDispatcher $instance;

    #[\Override]
    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->instance = new DomainEventDispatcher($this->eventDispatcher);
    }

    public function testDispatchWithNoEntity(): void
    {
        $this->eventDispatcher->expects($this->never())->method("dispatch");

        $this->instance->dispatch();
    }

    public function testDispatchWithNoEvent(): void
    {
        $this->eventDispatcher->expects($this->never())->method("dispatch");

        $entity = new DomainEventRecorderSample();
        $this->instance->dispatch($entity);
    }

    public function testDispatchWithOneEntity(): void
    {
        $this->eventDispatcher->expects($this->once())->method("dispatch")->with(DomainEventRecorderSample::generateEvent1());

        $entity = new DomainEventRecorderSample();
        $entity->doSomething();
        $this->instance->dispatch($entity);
    }

    public function testDispatchWithOneEntityAndTwoEvent(): void
    {
        $expectedArgs = [
            DomainEventRecorderSample::generateEvent1(),
            DomainEventRecorderSample::generateEvent2(),
        ];
        $callIndex = 0;
        $this->eventDispatcher
            ->expects($this->exactly(\count($expectedArgs)))
            ->method("dispatch")
            ->willReturnCallback(function (object $arg) use (&$callIndex, $expectedArgs): void {
                $this->assertEquals($expectedArgs[$callIndex], $arg);
                $callIndex++;
            })
        ;

        $entity = new DomainEventRecorderSample();
        $entity->doSomething();
        $entity->doSomethingElse();
        $this->instance->dispatch($entity);
    }

    public function testDispatchWithTwoEntities(): void
    {
        $expectedArgs = [
            DomainEventRecorderSample::generateEvent1(),
            DomainEventRecorderSample::generateEvent2(),
            DomainEventRecorderSample::generateEvent1(),
        ];
        $callIndex = 0;
        $this->eventDispatcher
            ->expects($this->exactly(\count($expectedArgs)))
            ->method("dispatch")
            ->willReturnCallback(function (object $arg) use (&$callIndex, $expectedArgs): void {
                $this->assertEquals($expectedArgs[$callIndex], $arg);
                $callIndex++;
            })
        ;

        $entity1 = new DomainEventRecorderSample();
        $entity1->doSomething();
        $entity2 = new DomainEventRecorderSample();
        $entity2->doSomethingElse();
        $entity2->doSomething();
        $this->instance->dispatch($entity1, $entity2);
    }
}
