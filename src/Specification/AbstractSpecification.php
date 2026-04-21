<?php

declare(strict_types = 1);

namespace Padi\Specification;

/**
 * @template TCandidate
 * @implements SpecificationInterface<TCandidate>
 */
abstract class AbstractSpecification implements SpecificationInterface
{
    /** @var string[] */
    private array $errors = [];

    #[\Override]
    public function isSatisfiedBy(mixed $candidate): bool
    {
        $this->errors = [];

        if ($this->doIsSatisfiedBy($candidate)) {
            return true;
        }

        $error = static::getError();
        if ($error !== "") {
            $this->addErrors($error);
        }

        return false;
    }

    #[\Override]
    public function getLastErrors(): array
    {
        return $this->errors;
    }

    protected function addErrors(string ...$errors): void
    {
        $this->errors = \array_merge($this->errors, $errors);
    }

    /** Override this method if you want return an error */
    protected function getError(): string
    {
        return "";
    }

    /**
     * @param TCandidate $candidate
     */
    abstract protected function doIsSatisfiedBy(mixed $candidate): bool;
}
