<?php

declare(strict_types=1);

namespace App\AI;

use Symfony\Component\Serializer\SerializerInterface;

final readonly class ChatHistory implements ChatHistoryInterface
{
    private const PREFIX = 'chat_';
    private const EXTENSION = '.json';

    public function __construct(
        private SerializerInterface $serializer,
        private string $directory,
        private string $prefix = self::PREFIX,
        private string $extension = self::EXTENSION,
    ) {
    }

    /**
     * @return ChatMessage[]
     */
    public function load(string $threadId): array
    {
        $filepath = $this->filepath($threadId);

        if (!file_exists($filepath)) {
            return [];
        }

        $content = file_get_contents($filepath);

        if (false === $content) {
            throw new \RuntimeException(sprintf('Could not read message-store file "%s".', $filepath));
        }

        /** @var ChatMessage[] $messages */
        $messages = $this->serializer->deserialize($content, ChatMessage::class.'[]', 'json');

        assert(\is_array($messages), sprintf('Invalid message-store payload for thread "%s".', $threadId));

        return $messages;
    }

    public function append(string $threadId, ChatMessage $message): void
    {
        $messages = $this->load($threadId);
        $this->store($threadId, [...$messages, $message]);
    }

    public function delete(string $threadId): void
    {
        $filepath = $this->filepath($threadId);

        if (file_exists($filepath) && !unlink($filepath)) {
            throw new \RuntimeException(sprintf('Could not delete the message-store file "%s".', $filepath));
        }
    }

    /**
     * @param ChatMessage[] $messages
     */
    private function store(string $threadId, array $messages): void
    {
        self::ensureDirectory($this->directory);

        $filepath = $this->filepath($threadId);
        $content = $this->serializer->serialize($messages, 'json');

        if (false === file_put_contents($filepath, $content)) {
            throw new \RuntimeException(sprintf('Could not write message-store file "%s".', $filepath));
        }
    }

    private function filepath(string $threadId): string
    {
        return sprintf('%s/%s%s%s', rtrim($this->directory, '/'), $this->prefix, $threadId, $this->extension);
    }

    private function ensureDirectory(string $directory): void
    {
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
            throw new \RuntimeException(sprintf('Could not create message-store directory "%s".', $directory));
        }
    }
}
