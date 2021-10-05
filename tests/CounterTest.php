<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents\Tests;

use Dsantang\DomainEvents\Counter;
use PHPUnit\Framework\TestCase;

final class CounterTest extends TestCase
{
    /**
     * @test
     */
    public function getNextReturnsIncreasingIntegers(): void
    {
        Counter::reset();
        self::assertEquals(0, Counter::getNext());
        self::assertEquals(1, Counter::getNext());
        self::assertEquals(2, Counter::getNext());
    }

    /**
     * @test
     */
    public function resetSetsTheCounterAt0(): void
    {
        Counter::getNext();
        Counter::reset();
        self::assertEquals(0, Counter::getNext());
    }
}
