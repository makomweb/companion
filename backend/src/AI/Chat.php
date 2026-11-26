<?php

declare(strict_types=1);

namespace App\AI;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\AI\Platform\Message\SystemMessage;
use Symfony\AI\Platform\Message\UserMessage;
use Symfony\AI\Platform\Result\Stream\Delta\DeltaInterface;

final class Chat
{
    public function __construct(
        private readonly AgentInterface $agent,
        private readonly ChatHistory $history,
    ) {
    }

    /**
     * Send a message and stream the response.
     *
     * @param non-empty-string $threadId
     *
     * @return \Generator<DeltaInterface>
     */
    public function stream(UserMessage $message, string $threadId): \Generator
    {
        $thread = $this->createThread($threadId)->with($message);

        $this->history->append($threadId, ChatMessage::fromSymfonyAI($message));

        $execution = $this->agent->call($thread, ['stream' => true]);

        foreach ($execution->asStream() as $chunk) {
            yield $chunk;
        }

        $this->history->append(
            $threadId,
            ChatMessage::fromSymfonyAI(
                Message::ofAssistant(
                    $execution->getResult()
                )
            )
        );
    }

    /**
     * @param non-empty-string $threadId
     */
    private function createThread(string $threadId): MessageBag
    {
        $thread = $this->history->load($threadId);

        return empty($thread)
            ? new MessageBag(
                new SystemMessage('You are a helpful and concise AI assistant. '
                    .'Provide answers in a maximum of 4 sentences. '
                    .'Be friendly and direct. '
                    .'Format your response as plain, easy-to-understand flowing text.'
                )
            )
            : new MessageBag(
                ...array_map(
                    static fn (ChatMessage $m) => $m->toSymfonyAI(),
                    $thread
                )
            );
    }
}
