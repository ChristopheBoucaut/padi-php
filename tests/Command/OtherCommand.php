<?php

declare(strict_types = 1);

namespace Padi\Tests\Command;

use Padi\Command\CommandInterface;

readonly class OtherCommand implements CommandInterface
{
    public function __construct(
        public string $data = "other fake",
    ) {
    }
}
