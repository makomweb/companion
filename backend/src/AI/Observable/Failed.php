<?php

declare(strict_types=1);

namespace App\AI\Observable;

final class Failed extends AbstractEvent implements \Stringable
{
    public function __construct(
        public readonly \Throwable $exception,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('💥 Chat error: %s!', $this->exception->getMessage());
    }
}
