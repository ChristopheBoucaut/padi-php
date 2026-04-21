<?php

declare(strict_types = 1);

namespace Padi\Tests\Specification;

use Padi\Specification\AbstractSpecification;

/** @extends AbstractSpecification<bool> */
class IsTrue extends AbstractSpecification
{
    public const string ERROR = "value is not true";

    #[\Override]
    protected function doIsSatisfiedBy(mixed $candidate): bool
    {
        return $candidate === true;
    }

    #[\Override]
    protected function getError(): string
    {
        return self::ERROR;
    }
}
