<?php

declare(strict_types = 1);

namespace Padi\Tests\Command;

use Padi\Command\CollectingCommandBus;
use PHPUnit\Framework\TestCase;

class CollectingCommandBusTest extends TestCase
{
    public function testDispatch(): void
    {
        $bus = new CollectingCommandBus();

        $cmd = new FakeCommand();
        $bus->dispatch($cmd);

        $this->assertSame([$cmd], $bus->dispatched);
    }
}
