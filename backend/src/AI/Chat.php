<?php

declare(strict_types=1);

namespace App\AI;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\AI\Platform\Message\SystemMessage;
use Symfony\AI\Platform\Result\Stream\Delta\TextDelta;

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
     * @param non-empty-string $message
     * @param non-empty-string $threadId
     *
     * @return \Generator<string>
     */
    public function stream(string $message, string $threadId): \Generator
    {
        $userMessage = Message::ofUser($message);

        $thread = $this->createThread($threadId)->with($userMessage);

        $this->history->append($threadId, ChatMessage::fromSymfonyAI($userMessage));

        $execution = $this->agent->call($thread, ['stream' => true]);

        foreach ($execution->asStream() as $chunk) {
            if ($chunk instanceof TextDelta) {
                yield (string) $chunk;
            }
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
