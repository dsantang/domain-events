<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents\Tests\Registry;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEvents\Registry\EventsRegistry;
use Dsantang\DomainEvents\Registry\IncompatibleClass;
use PHPUnit\Framework\TestCase;

use function assert;
use function current;
use function method_exists;

final class EventsRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function triggeringADomainEventFromAnIncompatibleClassThrowsAnException(): void
    {
        $this->expectException(IncompatibleClass::class);

        $this->instantiateAnIncompatibleClass(true);
    }

    /**
     * @test
     */
    public function triggeringADomainEventRecordsItInMemory(): EventAware
    {
        $event = self::generateEvent();

        $aggregate = new class ($event) implements EventAware {
            use EventsRegistry;

            public function __construct(DomainEvent $domainEvent)
            {
                $this->triggeredA(
                    $domainEvent
                );
            }
        };

        self::assertEquals($event, $aggregate->expelRecordedEvents()[0]);

        return $aggregate;
    }

    /**
     * @test
     */
    public function expellingTheDomainEventsReturnsThemAndEmptiesTheRegister(): void
    {
        $event = self::generateEvent();

        $aggregate = new class ($event) implements EventAware {
            use EventsRegistry;

            private DomainEvent $domainEvent;

            public function __construct(DomainEvent $domainEvent)
            {
                $this->domainEvent = $domainEvent;
                $this->triggeredA(
                    $domainEvent
                );
            }

            public function trigger(): void
            {
                $this->triggeredA($this->domainEvent);
            }
        };

        $aggregate->trigger();
        $triggeredEvents = $aggregate->expelRecordedEvents();
        self::assertCount(2, $triggeredEvents);
        self::assertSame($event, current($triggeredEvents));
        self::assertEmpty($aggregate->expelRecordedEvents());
    }

    /**
     * @test
     */
    public function expellingTheDomainEventsReturnsAnEmptyArrayIfNoEventsHaveBeenTriggered(): void
    {
        $aggregate = new class implements EventAware {
            use EventsRegistry;
        };

        self::assertEmpty($aggregate->expelRecordedEvents());
    }

    /**
     * @test
     */
    public function expellingTheDomainEventsFromAnIncompatibleClassThrowsAnException(): void
    {
        $this->expectException(IncompatibleClass::class);

        $incompatibleClass = $this->instantiateAnIncompatibleClass();

        assert(method_exists($incompatibleClass, 'expelRecordedEvents'));

        $incompatibleClass->expelRecordedEvents();
    }

    private static function generateEvent(): DomainEvent
    {
        return new class implements DomainEvent
        {
            public function getName(): string
            {
                return 'domain event name';
            }
        };
    }

    private function instantiateAnIncompatibleClass(bool $triggeringTheEvent = false): object
    {
        return new class (self::generateEvent(), $triggeringTheEvent)
        {
            use EventsRegistry;

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
