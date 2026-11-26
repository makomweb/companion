<?php

declare(strict_types=1);

namespace App\AI;

interface ChatHistoryInterface
{
    /**
     * @return ChatMessage[]
     */
    public function load(string $threadId): array;

    public function append(string $threadId, ChatMessage $message): void;

    public function delete(string $threadId): void;
}
