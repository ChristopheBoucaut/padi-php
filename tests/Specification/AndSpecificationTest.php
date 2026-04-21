<?php

declare(strict_types=1);

namespace Padi\Tests\Specification;

use Padi\Specification\AndSpecification;
use PHPUnit\Framework\TestCase;

class AndSpecificationTest extends TestCase
{
    public function testEmpty(): void
    {
        $specification = new AndSpecification();

        static::assertTrue($specification->isSatisfiedBy(null));
    }

    public function testWithOneSpecification(): void
    {
        $specification = new AndSpecification(new TrueSpecification());
        static::assertTrue($specification->isSatisfiedBy(null));

        $specification = new AndSpecification(new FalseSpecification());
        static::assertFalse($specification->isSatisfiedBy(null));
    }

    public function testWithMultipleSpecifications(): void
    {
        // Mix ok / ko
        $specification = new AndSpecification(
            new TrueSpecification(),
            new FalseSpecification(),
            new TrueSpecification(),
        );
        static::assertFalse($specification->isSatisfiedBy(null));

        // Mix ok
        $specification = new AndSpecification(
            new TrueSpecification(),
            new TrueSpecification(),
        );
        static::assertTrue($specification->isSatisfiedBy(null));

        // Mix ko
        $specification = new AndSpecification(
            new FalseSpecification(),
            new FalseSpecification(),
        );
        static::assertFalse($specification->isSatisfiedBy(null));
    }
}
