<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents;

interface DomainEvent
{
    public function getName(): string;
}
