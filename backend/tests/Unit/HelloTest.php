<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HelloTest extends TestCase
{
    #[Test]
    #[DataProvider('provideTestData')]
    public function run_me(bool $flag, string $message): void
    {
        self::assertEquals($flag, 'Hello world!' === $message);
    }

    /**
     * @return array<array{bool, string}>
     */
    public static function provideTestData(): array
    {
        return [
            [true, 'Hello world!'],
            [false, 'Hello user!'],
        ];
    }
}
