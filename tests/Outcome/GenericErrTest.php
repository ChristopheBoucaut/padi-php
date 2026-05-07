<?php

declare(strict_types = 1);

namespace Padi\Tests\Outcome;

use Padi\Outcome\GenericErr;
use PHPUnit\Framework\TestCase;

class GenericErrTest extends TestCase
{
    private const string ERR_MESSAGE = "this is an error message";
    private const array ERR_CONTEXT = ["simple", "context"];

    private GenericErr $instance;

    #[\Override]
    protected function setUp(): void
    {
        $this->instance = new GenericErr(self::ERR_MESSAGE, self::ERR_CONTEXT);
    }

    public function testGetMessage(): void
    {
        $this->assertSame(self::ERR_MESSAGE, $this->instance->getMessage());
    }

    public function testGetContext(): void
    {
        $this->assertSame(self::ERR_CONTEXT, $this->instance->getContext());
    }

    public function testToString(): void
    {
        $this->assertSame(self::ERR_MESSAGE, $this->instance->__toString());
    }

    public function testThrow(): void
    {
        $this->expectException(\Exception::class);
        $this->instance->throw();
    }

    public function testDefaultValue(): void
    {
        $err = new GenericErr(self::ERR_MESSAGE);
        $this->assertSame([], $err->getContext());
    }
}
