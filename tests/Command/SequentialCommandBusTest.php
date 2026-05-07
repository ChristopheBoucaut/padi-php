<?php

declare(strict_types = 1);

namespace Padi\Tests\Command;

use Padi\Command\SequentialCommandBus;
use PHPUnit\Framework\TestCase;

class SequentialCommandBusTest extends TestCase
{
    public function testDispatchWithoutHandler(): void
    {
        $this->expectException(\LogicException::class);

        $bus = new SequentialCommandBus();
        $bus->dispatch(new FakeCommand());
    }

    public function testDispatchOneCommand(): void
    {
        $bus = new SequentialCommandBus();

        $value = null;
        $bus->register(FakeCommand::class, static function (FakeCommand $cmd) use (&$value): void {
            $value = $cmd->data;
        });

        $cmd = new FakeCommand();
        $bus->dispatch($cmd);

        $this->assertSame($cmd->data, $value);
    }

    public function testDispatchTwoDistinctCommands(): void
    {
        $bus = new SequentialCommandBus();

        $value1 = null;
        $bus->register(FakeCommand::class, static function (FakeCommand $cmd) use (&$value1): void {
            $value1 = $cmd->data;
        });

        $value2 = null;
        $bus->register(OtherCommand::class, static function (OtherCommand $cmd) use (&$value2): void {
            $value2 = $cmd->data;
        });

        $cmd = new FakeCommand();
        $cmd2 = new OtherCommand();
        $bus->dispatch($cmd, $cmd2);

        $this->assertSame($cmd->data, $value1);
        $this->assertSame($cmd2->data, $value2);
    }

    public function testDispatchTwoSameCommands(): void
    {
        $bus = new SequentialCommandBus();

        $value = [];
        $bus->register(FakeCommand::class, static function (FakeCommand $cmd) use (&$value): void {
            $value[] = $cmd->data;
        });

        $cmd = new FakeCommand();
        $cmd2 = new FakeCommand("other fake");
        $bus->dispatch($cmd, $cmd2);

        $this->assertSame([$cmd->data, $cmd2->data], $value);
    }

    public function testOverrideHandler(): void
    {
        $bus = new SequentialCommandBus();

        $value1 = null;
        $bus->register(FakeCommand::class, static function (FakeCommand $cmd) use (&$value1): void {
            $value1 = $cmd->data;
        });
        $value2 = null;
        $bus->register(FakeCommand::class, static function (FakeCommand $cmd) use (&$value2): void {
            $value2 = $cmd->data;
        });

        $cmd = new FakeCommand();
        $bus->dispatch($cmd);

        $this->assertNull($value1);
        $this->assertSame($cmd->data, $value2);
    }
}
