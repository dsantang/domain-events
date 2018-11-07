<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents\Registry;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;

trait EventsRegistry
{
    /** @var DomainEvent[] */
    private $recordedEvents = [];

    /**
     * @return DomainEvent[]
     */
    public function expelRecordedEvents() : array
    {
        if ($this instanceof EventAware === false) {
            throw IncompatibleClass::forExpellingEvents($this);
        }

        $recordedEvents = $this->recordedEvents;

        $this->recordedEvents = [];

        return $recordedEvents;
    }

    private function triggeredA(DomainEvent $event) : void
    {
        if ($this instanceof EventAware === false) {
            throw IncompatibleClass::forTriggeringEvents($this);
        }

        $this->recordedEvents[] = $event;
    }
}
