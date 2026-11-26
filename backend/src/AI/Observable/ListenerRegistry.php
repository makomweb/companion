<?php

declare(strict_types=1);

namespace App\AI\Observable;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerRegistry implements ListenerProviderInterface
{
    /**
     * @var array<class-string, callable[]>
     */
    private array $listeners = [];

    /**
     * @param class-string $eventClass
     */
    public function subscribe(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $eventClass => $listeners) {
            if ($event instanceof $eventClass) {
                yield from $listeners;
            }
        }
    }
}
