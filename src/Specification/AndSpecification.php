<?php

declare(strict_types=1);

namespace Padi\Specification;

/**
 * @template TCandidate
 * @implements SpecificationInterface<TCandidate>
 */
class AndSpecification implements SpecificationInterface
{
    /** @var SpecificationInterface<TCandidate>[] */
    private readonly array $specifications;

    /**
     * @param SpecificationInterface<TCandidate> ...$specifications
     */
    public function __construct(
        SpecificationInterface ...$specifications
    ) {
        $this->specifications = $specifications;
    }

    #[\Override]
    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (empty($this->specifications)) {
            return true;
        }

        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($candidate)) {
                return false;
            }
        }

        return true;
    }
}
