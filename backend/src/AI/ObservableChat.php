<?php

declare(strict_types=1);

namespace App\AI;

use App\AI\Observable\AbstractEvent;
use App\AI\Observable\Dispatcher;
use App\AI\Observable\Failed;
use App\AI\Observable\Finished;
use App\AI\Observable\Received;
use App\AI\Observable\Started;
use Symfony\AI\Platform\Message\UserMessage;
use Symfony\AI\Platform\Result\Stream\Delta\TextDelta;

final readonly class ObservableChat
{
    public function __construct(
        private Chat $chat,
        private Dispatcher $dispatcher,
    ) {
    }

    /** @param callable(AbstractEvent): void $callback */
    public function subscribe(callable $callback): void
    {
        $this->dispatcher->subscribe(AbstractEvent::class, $callback(...));
    }

    /** @param non-empty-string $threadId */
    public function submit(UserMessage $message, string $threadId): void
    {
        $this->dispatcher->dispatch(new Started($message));

        try {
            foreach ($this->chat->stream($message, $threadId) as $chunk) {
                if ($chunk instanceof TextDelta) {
                    $this->dispatcher->dispatch(new Received($chunk));
                }
            }
        } catch (\Throwable $ex) {
            $this->dispatcher->dispatch(new Failed($ex));
        }

        $this->dispatcher->dispatch(new Finished());
    }
}
