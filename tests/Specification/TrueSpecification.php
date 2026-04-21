<?php

declare(strict_types=1);

namespace Padi\Tests\Specification;

use Padi\Specification\SpecificationInterface;

/** @implements SpecificationInterface<null> */
class TrueSpecification implements SpecificationInterface
{
    #[\Override]
    public function isSatisfiedBy(mixed $candidate): bool
    {
        return true;
    }
}
