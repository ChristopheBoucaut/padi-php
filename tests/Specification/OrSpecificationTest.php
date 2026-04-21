<?php

declare(strict_types=1);

namespace Padi\Tests\Specification;

use Padi\Specification\OrSpecification;
use PHPUnit\Framework\TestCase;

class OrSpecificationTest extends TestCase
{
    public function testEmpty(): void
    {
        $specification = new OrSpecification();

        static::assertTrue($specification->isSatisfiedBy(null));
    }

    public function testWithOneSpecification(): void
    {
        $specification = new OrSpecification(new TrueSpecification());
        static::assertTrue($specification->isSatisfiedBy(null));

        $specification = new OrSpecification(new FalseSpecification());
        static::assertFalse($specification->isSatisfiedBy(null));
    }

    public function testWithMultipleSpecifications(): void
    {
        // Mix ok / ko
        $specification = new OrSpecification(
            new TrueSpecification(),
            new FalseSpecification(),
            new TrueSpecification(),
        );
        static::assertTrue($specification->isSatisfiedBy(null));

        // Mix ok
        $specification = new OrSpecification(
            new TrueSpecification(),
            new TrueSpecification(),
        );
        static::assertTrue($specification->isSatisfiedBy(null));

        // Mix ko
        $specification = new OrSpecification(
            new FalseSpecification(),
            new FalseSpecification(),
        );
        static::assertFalse($specification->isSatisfiedBy(null));
    }
}
