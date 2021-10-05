<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents\Registry;

use Dsantang\DomainEvents\EventAware;
use RuntimeException;

use function get_class;
use function sprintf;

final class IncompatibleClass extends RuntimeException
{
    private const MESSAGE = 'Class "%s" must implement the "%s" interface in order to %s domain events.';

    public static function forExpellingEvents(object $instance): self
    {
        return new self(sprintf(self::MESSAGE, get_class($instance), EventAware::class, 'expel'));
    }

    public static function forTriggeringEvents(object $instance): self
    {
        return new self(sprintf(self::MESSAGE, get_class($instance), EventAware::class, 'trigger'));
    }
}
