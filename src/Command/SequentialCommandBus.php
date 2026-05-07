<?php

declare(strict_types = 1);

namespace Padi\Command;

/**
 * @template TCommand of CommandInterface
 */
class SequentialCommandBus implements CommandBusInterface
{
    /** @var array<class-string<TCommand>, callable(TCommand): void> */
    private array $handlers = [];

    /**
     * @param class-string<TCommand> $commandClass
     * @param callable(TCommand) $handler
     */
    public function register(string $commandClass, callable $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    /**
     * @throws \LogicException
     */
    #[\Override]
    public function dispatch(CommandInterface ...$commands): void
    {
        foreach ($commands as $command) {
            $handler = $this->handlers[$command::class] ?? throw new \LogicException(\sprintf("No handler for %s", $command::class));
            $handler($command);
        }
    }
}
