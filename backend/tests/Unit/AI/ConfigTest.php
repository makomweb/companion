<?php

declare(strict_types=1);

namespace App\Tests\Unit\AI;

use App\AI\Config;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    #[Test]
    public function creates_config_with_valid_arguments(): void
    {
        $config = new Config(
            providerEndpoint: 'http://localhost:8000/api',
            model: 'llama2',
        );

        self::assertSame('http://localhost:8000', $config->baseEndpoint);
        self::assertSame('http://localhost:8000/api', $config->apiEndpoint);
        self::assertSame('llama2', $config->model);
    }

    #[Test]
    public function normalizes_endpoint_with_trailing_slash(): void
    {
        $config = new Config(
            providerEndpoint: 'http://localhost:8000/api/',
            model: 'llama2',
        );

        self::assertSame('http://localhost:8000', $config->baseEndpoint);
        self::assertSame('http://localhost:8000/api', $config->apiEndpoint);
    }

    #[Test]
    public function normalizes_endpoint_with_multiple_trailing_slashes(): void
    {
        $config = new Config(
            providerEndpoint: 'http://localhost:8000/api///',
            model: 'llama2',
        );

        self::assertSame('http://localhost:8000', $config->baseEndpoint);
        self::assertSame('http://localhost:8000/api', $config->apiEndpoint);
    }

    #[Test]
    public function normalizes_endpoint_without_api_suffix(): void
    {
        $config = new Config(
            providerEndpoint: 'http://localhost:8000',
            model: 'llama2',
        );

        self::assertSame('http://localhost:8000', $config->baseEndpoint);
        self::assertSame('http://localhost:8000/api', $config->apiEndpoint);
    }

    #[Test]
    public function formats_to_string_correctly(): void
    {
        $config = new Config(
            providerEndpoint: 'http://localhost:8000/api',
            model: 'llama2',
        );

        self::assertSame('endpoint = http://localhost:8000/api, model = llama2', (string) $config);
    }
}
