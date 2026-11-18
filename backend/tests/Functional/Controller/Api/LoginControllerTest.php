<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Tests\Functional\DBAwareWebTestCase;
use PHPUnit\Framework\Attributes\Test;

class LoginControllerTest extends DBAwareWebTestCase
{
    #[Test]
    public function login_with_valid_credentials(): void
    {
        $client = self::getKernelBrowser();

        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
        ], \JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();
        assert(is_string($content));

        $response = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        assert(\is_array($response));
        assert(is_string($response['status'] ?? null));
        assert(\is_array($response['user'] ?? null));
        assert(is_string($response['user']['email'] ?? null));
        assert(\is_array($response['user']['roles'] ?? null));

        self::assertEquals('success', $response['status']);
        self::assertEquals(self::TEST_EMAIL, $response['user']['email']);
        self::assertContains('ROLE_USER', $response['user']['roles']);
    }

    #[Test]
    public function login_with_invalid_credentials(): void
    {
        $client = self::getKernelBrowser();

        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => self::TEST_EMAIL,
            'password' => 'wrongpassword',
        ], \JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(401);
        $content = $client->getResponse()->getContent();
        assert(is_string($content));
        self::assertStringContainsString('Invalid credentials', $content);
    }

    #[Test]
    public function login_with_missing_fields(): void
    {
        $client = self::getKernelBrowser();

        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => self::TEST_EMAIL,
        ], \JSON_THROW_ON_ERROR));

        self::assertResponseStatusCodeSame(400);
        $content = $client->getResponse()->getContent();
        assert(is_string($content));
        self::assertStringContainsString('Email and password are required', $content);
    }
}
