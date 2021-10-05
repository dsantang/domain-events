<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents;

interface EventAware
{
    /**
     * @return DomainEvent[]
     */
    public function expelRecordedEvents(): array;
}
