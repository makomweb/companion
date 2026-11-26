<?php

declare(strict_types=1);

namespace App\Command;

use App\AI\Observable\AbstractEvent;
use App\AI\Observable\Failed;
use App\AI\Observable\Finished;
use App\AI\Observable\Received;
use App\AI\Observable\Started;
use Symfony\Component\Console\Style\SymfonyStyle;

final readonly class ChatEventHandler
{
    public function __construct(private SymfonyStyle $io)
    {
    }

    public function __invoke(AbstractEvent $event): void
    {
        match (true) {
            $event instanceof Started => $this->onStarted($event),
            $event instanceof Received => $this->onReceived($event),
            $event instanceof Finished => $this->onFinished($event),
            $event instanceof Failed => $this->onFailed($event),
            default => throw new \Exception(sprintf('Unsupported event "%s"', get_debug_type($event))),
        };
    }

    private function onStarted(Started $event): void
    {
        $this->io->write('🤖 Agent: ');
    }

    private function onReceived(Received $event): void
    {
        $this->io->write((string) $event);
    }

    private function onFinished(Finished $event): void
    {
        $this->io->newLine();
    }

    private function onFailed(Failed $event): void
    {
        $this->io->error((string) $event);
    }
}
