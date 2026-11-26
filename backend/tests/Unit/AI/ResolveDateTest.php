<?php

declare(strict_types=1);

namespace App\Tests\Unit\AI;

use App\AI\Tool\ResolveDate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ResolveDateTest extends TestCase
{
    #[Test]
    #[DataProvider('provideUserDateInputs')]
    public function resolves_natural_language_to_iso_date(string $userInput, string $expectedDate): void
    {
        $tool = new ResolveDate(new \DateTimeImmutable('2026-11-25'));
        $result = ($tool)($userInput);

        self::assertSame($expectedDate, $result);
    }

    /**
     * @return array<array{string, string}>
     */
    public static function provideUserDateInputs(): array
    {
        return [
            'today' => ['today', '2026-11-25'],
            'yesterday' => ['yesterday', '2026-11-24'],
            'tomorrow' => ['tomorrow', '2026-11-26'],
            '2 days ago' => ['2 days ago', '2026-11-23'],
            'in 2 days' => ['in 2 days', '2026-11-27'],
            'in 1 week' => ['in 1 week', '2026-12-02'],
            'last monday' => ['last monday', '2026-11-23'],
            '3 days ago' => ['3 days ago', '2026-11-22'],
        ];
    }
}
