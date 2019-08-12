# Domain Events
[![CircleCI](https://circleci.com/gh/dsantang/domain-events/tree/master.svg?style=svg)](https://circleci.com/gh/dsantang/domain-events/tree/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dsantang/domain-events/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dsantang/domain-events/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/dsantang/domain-events/badges/build.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events/build-status/master)
[![SymfonyInsight](https://insight.symfony.com/projects/d2302e70-4903-4ec7-aedd-3ea8bc71d217/small.svg)](https://insight.symfony.com/projects/d2302e70-4903-4ec7-aedd-3ea8bc71d217)

A simple package that guides the creation and dispatching of domain events.

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
php composer.phar require dsantang/domain-events
```

## Usage

This package is meant to provide an **abstraction for your application's domain events**.
Domain events are events that can be raised by your Aggregates during a domain transaction.
They capture an occurrence of something that happened in your domain.
An example of a domain event is as follows:

```php
<?php

use Dsantang\DomainEvents\DomainEvent;

final class OrderSent implements DomainEvent, DeletionAware
{
    public const EVENT_NAME = 'order-sent';

    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var DateTimeImmutable
     */
    private $timestamp;

    public function __construct(OrderId $orderId, Address $address, DateTimeImmutable $timestamp)
    {
        // ...
    }

    /**
     * This is the only interface method that needs to be implemented.
     * This method should return an application-unique event name.
     */
    public function getEventName(): string
    {
        return self::EVENT_NAME;
    }
    
    public function expelDeletionEvents(): DomainEvent
    {
        return new OrderDeleted();
    }

    // ...
}
```
### Aggregate deletions

Sometimes the deletion of an aggregate is a relevant event too. In this case tho, the aggregate cannot throw the event 
itself since it is most likely being deleted by your ORM.   
To work around this problem, you can implement the `DeletionAware` interface to signal your ORM that the deletion of that
aggregate should raise an event.

## Using transactions
Domain events can, and should, be momentarily cached in your aggregate, waiting until the transaction is completed.
Once the transaction is finished, and your aggregate state has **correctly been persisted** in your data storage,
the domain events that have occurred should be properly dealt with by your application.

In order to momentarily **cache/retrieve those events in your aggregate**, you can rely upon the simple `EventsRegistry` trait
provided by this library.

Example:

```php
final class Order implements EventAware
{
    /**
     * Use this trait to cache your domain event.
     */
    use EventsRegistry;

    // ...

    public function send(): void
    {
        // Domain logic is executed.

        // ...

        // Once the domain transaction is completed, the event must be cached via this method call:
        $this->triggeredA(new OrderSent(...));
    }

    // ...
}
```

Since every application has its own way of persisting the state of aggregates,
**this library doesn't provide an automated way to dispatch those events**, and it's up to the implementor to do that.

Those using `Doctrine`'s ORM can rely upon an [out of the box event dispatching automation](https://github.com/dsantang/domain-events-doctrine),
which only kicks in once a Doctrine's transaction is completed.
The package name is:

```
dsantang/domain-events-doctrine
```
