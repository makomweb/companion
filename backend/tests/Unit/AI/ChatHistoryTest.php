<?php

declare(strict_types=1);

namespace App\Tests\Unit\AI;

use App\AI\ChatMessage;
use App\Tests\Double\InMemoryChatHistory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\AI\Platform\Message\Message;

class ChatHistoryTest extends TestCase
{
    #[Test]
    public function load_empty_history_returns_empty_array(): void
    {
        $history = new InMemoryChatHistory();

        $messages = $history->load('thread-1');

        self::assertSame([], $messages);
    }

    #[Test]
    public function append_message_stores_in_thread(): void
    {
        $history = new InMemoryChatHistory();
        $message = ChatMessage::fromSymfonyAI(Message::ofUser('Hello'));

        $history->append('thread-1', $message);
        $loaded = $history->load('thread-1');

        self::assertCount(1, $loaded);
        self::assertSame($message, $loaded[0]);
    }

    #[Test]
    public function append_multiple_messages_maintains_order(): void
    {
        $history = new InMemoryChatHistory();
        $msg1 = ChatMessage::fromSymfonyAI(Message::ofUser('First'));
        $msg2 = ChatMessage::fromSymfonyAI(Message::ofAssistant('Second'));
        $msg3 = ChatMessage::fromSymfonyAI(Message::ofUser('Third'));

        $history->append('thread-1', $msg1);
        $history->append('thread-1', $msg2);
        $history->append('thread-1', $msg3);

        $loaded = $history->load('thread-1');

        self::assertCount(3, $loaded);
        self::assertSame($msg1, $loaded[0]);
        self::assertSame($msg2, $loaded[1]);
        self::assertSame($msg3, $loaded[2]);
    }

    #[Test]
    public function delete_removes_thread(): void
    {
        $history = new InMemoryChatHistory();
        $history->append('thread-1', ChatMessage::fromSymfonyAI(Message::ofUser('Message')));
        $history->delete('thread-1');

        self::assertSame([], $history->load('thread-1'));
    }

    #[Test]
    public function separate_threads_maintain_independent_history(): void
    {
        $history = new InMemoryChatHistory();
        $msg1 = ChatMessage::fromSymfonyAI(Message::ofUser('Thread 1 message'));
        $msg2 = ChatMessage::fromSymfonyAI(Message::ofUser('Thread 2 message'));

        $history->append('thread-1', $msg1);
        $history->append('thread-2', $msg2);

        self::assertCount(1, $history->load('thread-1'));
        self::assertCount(1, $history->load('thread-2'));
        self::assertSame($msg1, $history->load('thread-1')[0]);
        self::assertSame($msg2, $history->load('thread-2')[0]);
    }
}
