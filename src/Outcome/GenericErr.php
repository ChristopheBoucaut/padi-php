<?php

declare(strict_types = 1);

namespace Padi\Outcome;

class GenericErr implements ErrInterface
{
    /**
     * @param array<string|\Stringable> $context
     */
    public function __construct(
        private string $message,
        private array $context = [],
    ) {
    }

    #[\Override]
    public function getMessage(): string
    {
        return $this->message;
    }

    #[\Override]
    public function getContext(): array
    {
        return $this->context;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->message;
    }

    /** @throws \Exception */
    public function throw(): never
    {
        throw new \Exception((string) $this);
    }
}
