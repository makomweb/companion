<?php

declare(strict_types=1);

namespace App\AI;

use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolOutput;
use NeuronAI\Tools\ToolProperty;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.neuron_tool')]
final class ResolveDate extends Tool
{
    protected string $name = 'resolve_date';

    protected ?string $description = 'Resolve natural language or relative date expressions to ISO date (YYYY-MM-DD). '
        .'Supports English input like "in 2 days", "2 days ago", "yesterday", "last monday".';

    public function __construct(
        private \DateTimeImmutable $currentDate = new \DateTimeImmutable('now'),
    ) {
    }

    protected function properties(): array
    {
        return [
            ToolProperty::make(
                name: 'input',
                type: PropertyType::STRING,
                description: 'The user\'s relative date expression '
                    .'(e.g., "yesterday", "last friday", "3 days ago", "next monday")',
                required: true,
            ),
        ];
    }

    public function __invoke(string $input): ToolOutput
    {
        try {
            return ToolOutput::text(
                $this->currentDate
                    ->modify(self::normalize($input))
                    ->format('Y-m-d')
            );
        } catch (\Throwable $ex) {
            return ToolOutput::error(
                sprintf('Converting "%s" has failed because of %s!', $input, $ex->getMessage())
            );
        }
    }

    /**
     * Normalize natural language input into DateTimeImmutable::modify() digestible.
     */
    private static function normalize(string $input): string
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
