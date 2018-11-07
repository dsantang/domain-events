<?php

declare(strict_types=1);

namespace Dsantang\DomainEvents;

interface Dispatcher
{
    public function addListener(object $listener) : void;

    public function dispatch(DomainEvent $event) : void;
}
