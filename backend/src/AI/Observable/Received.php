<?php

declare(strict_types=1);

namespace App\AI\Observable;

use Symfony\AI\Platform\Result\Stream\Delta\TextDelta;

final class Received extends AbstractEvent implements \Stringable
{
    public function __construct(
        public readonly TextDelta $chunk,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->chunk;
    }
}
