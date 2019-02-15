<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents;

final class Counter
{
    /** @var int */
    private static $count = 0;

    public static function getNext() : int
    {
        return self::$count++;
    }

    public static function reset() : void
    {
        self::$count = 0;
    }
}
