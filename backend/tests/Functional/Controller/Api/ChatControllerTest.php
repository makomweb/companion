<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChatControllerTest extends WebTestCase
{
    /**
     * Chat API is public for UI usage (no authentication required).
     * This test is skipped in automated tests since Ollama is not available in test environment.
     * Integration tests with Ollama are run manually with docker-compose.
     */
    public function test_chat_message_route_exists(): void
    {
        $this->markTestSkipped('Chat API requires Ollama to be running (manual test only)');
    }
}
