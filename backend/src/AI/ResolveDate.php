<?php

declare(strict_types=1);

namespace App\AI;

use Symfony\AI\Agent\Toolbox\Attribute\AsTool;

#[AsTool(
    name: 'resolve_date',
    description: 'Resolve natural language or relative date expressions to ISO date (YYYY-MM-DD). '
        .'Supports English input like "in 2 days", "2 days ago", "yesterday", "last monday".'
)]
final readonly class ResolveDate
{
    public function __construct(
        private \DateTimeImmutable $date = new \DateTimeImmutable('now'),
    ) {
    }

    public function __invoke(string $input): string
    {
        // Step 1: Normalize natural language input to modify format
        $normalized = $this->normalize($input);

        // Step 2: Convert to ISO date
        return $this->date->modify($normalized)->format('Y-m-d');
    }

    private function normalize(string $input): string
    {
        $dateString = trim(mb_strtolower($input, 'UTF-8'));

        // Passthrough simple cases
        if (\in_array($dateString, ['today', 'yesterday', 'tomorrow'], true)) {
            return $dateString;
        }

        // Convert "X days ago" to "-X days"
        if (preg_match('/^(\d+)\s+days?\s+ago$/', $dateString, $matches)) {
            return sprintf('-%d days', (int) $matches[1]);
        }

        // Convert "in X days" to "+X days"
        if (preg_match('/^in\s+(\d+)\s+days?$/', $dateString, $matches)) {
            return sprintf('+%d days', (int) $matches[1]);
        }

        // Convert "last <weekday>" to "last <weekday>"
        if (preg_match('/^last\s+([a-z]+)$/', $dateString, $matches)) {
            return sprintf('last %s', $matches[1]);
        }

        // Convert "next <weekday>" to "next <weekday>"
        if (preg_match('/^next\s+([a-z]+)$/', $dateString, $matches)) {
            return sprintf('next %s', $matches[1]);
        }

        // Convert "X weeks ago" to "-X weeks"
        if (preg_match('/^(\d+)\s+weeks?\s+ago$/', $dateString, $matches)) {
            return sprintf('-%d weeks', (int) $matches[1]);
        }

        // Convert "in X weeks" to "+X weeks"
        if (preg_match('/^in\s+(\d+)\s+weeks?$/', $dateString, $matches)) {
            return sprintf('+%d weeks', (int) $matches[1]);
        }

        // Convert "X months ago" to "-X months"
        if (preg_match('/^(\d+)\s+months?\s+ago$/', $dateString, $matches)) {
            return sprintf('-%d months', (int) $matches[1]);
        }

        // Convert "in X months" to "+X months"
        if (preg_match('/^in\s+(\d+)\s+months?$/', $dateString, $matches)) {
            return sprintf('+%d months', (int) $matches[1]);
        }

        // If no pattern matches, return as-is (might work with PHP's modify)
        return $dateString;
    }
}
