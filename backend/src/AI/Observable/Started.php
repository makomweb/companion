<?php

declare(strict_types=1);

namespace App\AI\Observable;

use Symfony\AI\Platform\Message\UserMessage;

final class Started extends AbstractEvent
{
    public function __construct(
        public readonly UserMessage $message,
    ) {
    }
}
