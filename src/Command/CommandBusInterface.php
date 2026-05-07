<?php

declare(strict_types = 1);

namespace Padi\Command;

interface CommandBusInterface
{
    public function dispatch(CommandInterface ...$commands): void;
}
