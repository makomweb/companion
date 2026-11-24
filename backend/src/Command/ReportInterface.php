<?php

declare(strict_types=1);

namespace App\Command;

interface ReportInterface
{
    public function start(string $message): void;

    public function write(string $chunk): void;

    public function finish(): void;
}
