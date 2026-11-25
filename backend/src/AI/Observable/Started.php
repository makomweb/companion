<?php

declare(strict_types=1);

namespace App\AI\Observable;

use NeuronAI\Chat\Messages\UserMessage;

final class Started extends AbstractEvent
{
    public function __construct(
        public readonly UserMessage $message,
    ) {
    }
}
