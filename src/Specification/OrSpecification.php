<?php

declare(strict_types = 1);

namespace Padi\Specification;

/**
 * @template TCandidate
 * @extends AbstractSpecification<TCandidate>
 */
class OrSpecification extends AbstractSpecification
{
    /** @var SpecificationInterface<TCandidate>[] */
    private readonly array $specifications;

    /**
     * @param SpecificationInterface<TCandidate> ...$specifications
     */
    public function __construct(
        SpecificationInterface ...$specifications,
    ) {
        $this->specifications = $specifications;
    }

    #[\Override]
    protected function doIsSatisfiedBy(mixed $candidate): bool
    {
        if (empty($this->specifications)) {
            return true;
        }

        $errors = [];
        foreach ($this->specifications as $specification) {
            if ($specification->isSatisfiedBy($candidate)) {
                return true;
            }
            $errors = \array_merge($errors, $specification->getLastErrors());
        }
        $this->addErrors(...$errors);

        return false;
    }
}
