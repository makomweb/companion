<?php

declare(strict_types=1);

namespace App\AI;

use Symfony\AI\Platform\Message\AssistantMessage;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\UserMessage;

final class ChatMessage
{
    public function __construct(
        public readonly string $role,
        public readonly string $content,
    ) {
    }

    public static function fromSymfonyAI(UserMessage|AssistantMessage $message): self
    {
        return new self(
            $message->getRole()->value,
            $message->asText() ?? ''
        );
    }

    public function toSymfonyAI(): UserMessage|AssistantMessage
    {
        return match ($this->role) {
            'user' => Message::ofUser($this->content),
            'assistant' => Message::ofAssistant($this->content),
            default => throw new \Exception(sprintf('Unsupported chat role "%s"!', $this->role)),
        };
    }
}
