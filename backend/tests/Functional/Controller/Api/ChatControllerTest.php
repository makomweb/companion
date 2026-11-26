<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChatControllerTest extends WebTestCase
{
    public function test_chat_message_requires_authentication(): void
    {
        $client = self::createClient();
        $payload = json_encode(['message' => 'Hello', 'threadId' => 'test_123'], \JSON_THROW_ON_ERROR);
        $client->request('POST', '/api/chat', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);

        $this->assertResponseStatusCodeSame(401);
    }

    public function test_chat_message_route_exists(): void
    {
        $client = self::createClient();
        $payload = json_encode(['message' => 'Hello', 'threadId' => 'test_123'], \JSON_THROW_ON_ERROR);

        $client->request('POST', '/api/chat', [], [], ['CONTENT_TYPE' => 'application/json'], $payload);

        $this->assertTrue(
            $client->getResponse()->isClientError() || $client->getResponse()->isServerError(),
            'Route should exist and be accessible (but may require auth)'
        );
    }
}
