<?php

declare(strict_types = 1);

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
        $specification = new OrSpecification(new IsTrue());

        /** @NOTE : Start with false value to check errors is reset for second call */
        static::assertFalse($specification->isSatisfiedBy(false));
        static::assertSame([IsTrue::ERROR], $specification->getLastErrors());

        static::assertTrue($specification->isSatisfiedBy(true));
        static::assertSame([], $specification->getLastErrors());
    }

    public function testWithMultipleSpecifications(): void
    {
        // Mix ok / ko
        $specification = new OrSpecification(new IsTrue(), new IsFalse(), new IsTrue());
        static::assertTrue($specification->isSatisfiedBy(true));
        static::assertSame([], $specification->getLastErrors());

        static::assertTrue($specification->isSatisfiedBy(false));
        static::assertSame([], $specification->getLastErrors());

        // Mix ok
        $specification = new OrSpecification(new IsTrue(), new IsTrue());
        static::assertTrue($specification->isSatisfiedBy(true));
        static::assertSame([], $specification->getLastErrors());

        // Mix ko
        static::assertFalse($specification->isSatisfiedBy(false));
        static::assertSame([IsTrue::ERROR, IsTrue::ERROR], $specification->getLastErrors());
    }
}
