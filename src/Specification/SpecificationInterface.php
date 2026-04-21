<?php

declare(strict_types=1);

namespace Padi\Specification;

/**
 * @template TCandidate
 */
interface SpecificationInterface
{
    /**
     * @param TCandidate $candidate
     */
    public function isSatisfiedBy(mixed $candidate): bool;
}
