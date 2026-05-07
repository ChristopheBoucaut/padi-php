<?php

declare(strict_types = 1);

namespace Padi\Command;

/**
 * Useful to replace bus during test.
 */
class CollectingCommandBus implements CommandBusInterface
{
    /** @var CommandInterface[] */
    public private(set) array $dispatched = [];

    #[\Override]
    public function dispatch(CommandInterface ...$commands): void
    {
        foreach ($commands as $command) {
            $this->dispatched[] = $command;
        }
    }
}
