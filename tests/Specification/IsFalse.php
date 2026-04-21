<?php

declare(strict_types = 1);

namespace Padi\Tests\Specification;

use Padi\Specification\AbstractSpecification;

/**
 * @NOTE : We don't override getError to test when we have empty string from default value.
 *
 * @extends AbstractSpecification<bool>
 */
class IsFalse extends AbstractSpecification
{
    #[\Override]
    protected function doIsSatisfiedBy(mixed $candidate): bool
    {
        return $candidate === false;
    }
}
