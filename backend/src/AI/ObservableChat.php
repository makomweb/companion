<?php

declare(strict_types=1);

namespace App\AI;

use App\AI\Observable\AbstractEvent;
use App\AI\Observable\Dispatcher;
use App\AI\Observable\Finished;
use App\AI\Observable\Received;
use App\AI\Observable\Started;
use NeuronAI\Chat\Messages\Stream\Chunks\TextChunk;
use NeuronAI\Chat\Messages\UserMessage;

final readonly class ObservableChat
{
    public function __construct(
        private Chat $chat,
        private Dispatcher $dispatcher,
    ) {
    }

    /**
     * @param callable(AbstractEvent): void $callback
     */
    public function subscribe(callable $callback): void
    {
        $this->dispatcher->subscribe(AbstractEvent::class, $callback(...));
    }

    public function submit(UserMessage $message): void
    {
        $this->dispatcher->dispatch(new Started($message));

        foreach ($this->chat->stream($message) as $chunk) {
            if ($chunk instanceof TextChunk) {
                $this->dispatcher->dispatch(new Received($chunk));
            }
        }

        $this->dispatcher->dispatch(new Finished());
    }

    public function setThreadId(string $threadId): void
    {
        $this->chat->setThreadId($threadId);
    }
}
