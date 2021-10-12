<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents\Registry;

use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;

trait OrderedEventRegistry
{
    /** @var DomainEvent[] */
    private array $recordedEvents = [];

    /**
     * @return DomainEvent[]
     *
     * @throws IncompatibleClass
     */
    public function expelRecordedEvents(): array
    {
        if ($this instanceof EventAware === false) {
            throw IncompatibleClass::forExpellingEvents($this);
        }

        $recordedEvents = $this->recordedEvents;

        $this->recordedEvents = [];

        return $recordedEvents;
    }

    /**
     * @throws IncompatibleClass
     */
    private function triggeredA(DomainEvent $event): void
    {
        if ($this instanceof EventAware === false) {
            throw IncompatibleClass::forTriggeringEvents($this);
        }

        $this->recordedEvents[Counter::getNext()] = $event;
    }
}
