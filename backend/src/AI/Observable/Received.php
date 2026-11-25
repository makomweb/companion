<?php

declare(strict_types=1);

namespace App\AI\Observable;

use NeuronAI\Chat\Messages\Stream\Chunks\TextChunk;

final class Received extends AbstractEvent implements \Stringable
{
    public function __construct(
        public readonly TextChunk $chunk,
    ) {
    }

    public function __toString(): string
    {
        return $this->chunk->content;
    }
}
