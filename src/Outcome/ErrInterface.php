<?php

declare(strict_types = 1);

namespace Padi\Outcome;

interface ErrInterface extends ResultInterface, \Stringable
{
    public function getMessage(): string;

    /** @return array<string|\Stringable> */
    public function getContext(): array;
}
