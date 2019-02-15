<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents\Tests\Registry;

use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEvents\Registry\IncompatibleClass;
use Dsantang\DomainEvents\Registry\OrderedEventRegistry;
use PHPUnit\Framework\TestCase;
use function assert;
use function current;
use function method_exists;

final class OrderedEventRegistryTest extends TestCase
{
    private const RECORDED_EVENTS_ATTRIBUTE = 'recordedEvents';

    /**
     * @test
     */
    public function triggeringADomainEventFromAnIncompatibleClassThrowsAnException() : void
    {
        $this->expectException(IncompatibleClass::class);

        $this->instantiateAnIncompatibleClass(true);
    }

    /**
     * @test
     */
    public function triggeringADomainEventRecordsItInMemory() : EventAware
    {
        $event = self::generateEvent();

        $aggregate = new class ($event) implements EventAware {
            use OrderedEventRegistry;

            public function __construct(DomainEvent $domainEvent)
            {
                $this->triggeredA(
                    $domainEvent
                );
            }
        };

        self::assertAttributeContains($event, self::RECORDED_EVENTS_ATTRIBUTE, $aggregate);
        self::assertEquals(2, Counter::getNext());

        return $aggregate;
    }

    /**
     * @depends triggeringADomainEventRecordsItInMemory
     * @test
     */
    public function expellingTheDomainEventsReturnsThemAndEmptiesTheRegister(EventAware $aggregate) : EventAware
    {
        $triggeredEvents = $aggregate->expelRecordedEvents();

        self::assertCount(1, $triggeredEvents);
        self::assertEquals(current($triggeredEvents), self::generateEvent());
        self::assertAttributeEquals([], self::RECORDED_EVENTS_ATTRIBUTE, $aggregate);

        return $aggregate;
    }

    /**
     * @depends expellingTheDomainEventsReturnsThemAndEmptiesTheRegister
     * @test
     */
    public function expellingTheDomainEventsReturnsAnEmptyArrayIfNoEventsHaveBeenTriggered(EventAware $aggregate) : void
    {
        $triggeredEvents = $aggregate->expelRecordedEvents();

        self::assertCount(0, $triggeredEvents);
        self::assertAttributeEquals([], self::RECORDED_EVENTS_ATTRIBUTE, $aggregate);
    }

    /**
     * @test
     */
    public function expellingTheDomainEventsFromAnIncompatibleClassThrowsAnException() : void
    {
        $this->expectException(IncompatibleClass::class);

        $incompatibleClass = $this->instantiateAnIncompatibleClass();

        assert(method_exists($incompatibleClass, 'expelRecordedEvents'));

        $incompatibleClass->expelRecordedEvents();
    }

    private static function generateEvent() : DomainEvent
    {
        return new class implements DomainEvent
        {
            public function getName() : string
            {
                return 'domain event name';
            }
        };
    }

    private function instantiateAnIncompatibleClass(bool $triggeringTheEvent = false) : object
    {
        return new class (self::generateEvent(), $triggeringTheEvent)
        {
            use OrderedEventRegistry;

            public function __construct(DomainEvent $domainEvent, bool $triggeringTheEvent)
            {
                if ($triggeringTheEvent === false) {
                    return;
                }

                $this->triggeredA(
                    $domainEvent
                );
            }
        };
    }
}
