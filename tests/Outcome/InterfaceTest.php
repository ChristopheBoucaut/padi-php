<?php

declare(strict_types = 1);

namespace Padi\Tests\Outcome;

use Padi\Outcome\ErrInterface;
use Padi\Outcome\OkInterface;
use Padi\Outcome\ResultInterface;
use PHPUnit\Framework\TestCase;

class InterfaceTest extends TestCase
{
    public function testOkInterfaceImplementResultInterface(): void
    {
        $ok = new class implements OkInterface {};
        $this->assertInstanceOf(ResultInterface::class, $ok);
    }

    public function testErrInterfaceImplementResultInterface(): void
    {
        $err = new class implements ErrInterface {
            #[\Override]
            public function getMessage(): string
            {
                return "";
            }

            #[\Override]
            public function getContext(): array
            {
                return [];
            }

            #[\Override]
            public function __toString(): string
            {
                return "";
            }
        };
        $this->assertInstanceOf(ResultInterface::class, $err);
    }
}
