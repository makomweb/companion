<?php

declare(strict_types=1);

namespace App\Command;

use NeuronAI\Chat\Messages\Stream\Chunks\TextChunk;

interface ReportInterface
{
    public function write(string $message): void;

    public function onChunk(TextChunk $chunk): void;

    public function finish(): void;
}
