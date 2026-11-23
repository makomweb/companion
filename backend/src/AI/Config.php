<?php

declare(strict_types=1);

namespace App\AI;

use App\Assert\Assert;

final class Config implements \Stringable
{
    /**
     * @param non-empty-string $providerEndpoint
     * @param non-empty-string $model
     */
    public function __construct(
        private readonly string $providerEndpoint,
        public readonly string $model,
    ) {
        Assert::that(
            !empty($providerEndpoint),
            'Argument "providerEndpoint" is invalid!'
        );
        Assert::that(
            !empty($model),
            'Argument "model" is invalid!'
        );
    }

    public string $baseEndpoint {
        get => rtrim((string) preg_replace('#/api/?$#', '', rtrim($this->providerEndpoint, '/')), '/');
    }

    public string $apiEndpoint {
        get => $this->baseEndpoint.'/api';
    }

    public function __toString(): string
    {
        return "endpoint = {$this->apiEndpoint}, model = {$this->model}";
    }
}
