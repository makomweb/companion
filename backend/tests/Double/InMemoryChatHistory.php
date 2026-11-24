<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\AI\ChatHistoryInterface;
use App\AI\ChatMessage;

final class InMemoryChatHistory implements ChatHistoryInterface
{
    /**
     * @var array<string, ChatMessage[]>
     */
    private array $threads = [];

    /**
     * @return ChatMessage[]
     */
    public function load(string $threadId): array
    {
        return $this->threads[$threadId] ?? [];
    }

    public function append(string $threadId, ChatMessage $message): void
    {
        if (!isset($this->threads[$threadId])) {
            $this->threads[$threadId] = [];
        }

        $this->threads[$threadId][] = $message;
    }

    public function delete(string $threadId): void
    {
        unset($this->threads[$threadId]);
    }
}
