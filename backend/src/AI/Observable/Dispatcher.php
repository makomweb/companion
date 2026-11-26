<?php

declare(strict_types=1);

namespace App\AI\Observable;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class Dispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly ListenerRegistry $registry,
    ) {
    }

    /**
     * @param class-string $eventClass
     */
    public function subscribe(string $eventClass, callable $listener): void
    {
        $this->registry->subscribe($eventClass, $listener);
    }

    public function dispatch(object $event): object
    {
        /** @var callable $listener */
        foreach ($this->registry->getListenersForEvent($event) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }

            $listener($event);
        }

        return $event;
    }
}
