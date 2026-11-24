<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\AI\Chat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ChatResponse extends StreamedResponse
{
    /**
     * @param non-empty-string $message
     * @param non-empty-string $threadId
     */
    private function __construct(Chat $chat, string $message, string $threadId)
    {
        parent::__construct(
            callbackOrChunks: self::createCallback($chat, $message, $threadId),
            status: Response::HTTP_OK,
            headers: self::createHeaders(),
        );
    }

    /** @param array{message: string, thread_id: string} $data */
    public static function create(Chat $chat, array $data): self
    {
        $message = $data['message'];
        $threadId = $data['thread_id'];

        assert(!empty($message), 'Message should not be empty!');
        assert(!empty($threadId), 'Thread ID should not be empty!');

        return new self($chat, $message, $threadId);
    }

    /**
     * @param non-empty-string $message
     * @param non-empty-string $threadId
     */
    private static function createCallback(Chat $chat, string $message, string $threadId): callable
    {
        return static function () use ($chat, $message, $threadId): void {
            try {
                foreach ($chat->stream($message, $threadId) as $chunk) {
                    self::sendChunk($chunk);
                }

                self::sendDone();
            } catch (\Throwable $exception) {
                self::sendError($exception->getMessage());
            }
        };
    }

    private static function sendChunk(string $chunk): void
    {
        echo 'data: '.json_encode([
            'type' => 'text',
            'content' => $chunk,
        ], \JSON_THROW_ON_ERROR)."\n\n";
        flush();
    }

    private static function sendDone(): void
    {
        echo 'data: '.json_encode([
            'type' => 'done',
        ], \JSON_THROW_ON_ERROR)."\n\n";
    }

    private static function sendError(string $message): void
    {
        echo 'data: '.json_encode([
            'type' => 'error',
            'content' => $message,
        ], \JSON_THROW_ON_ERROR)."\n\n";
    }

    /**
     * @return array<string, string>
     */
    private static function createHeaders(): array
    {
        return [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ];
    }
}
