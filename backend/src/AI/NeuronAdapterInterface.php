<?php

declare(strict_types=1);

namespace App\AI;

use NeuronAI\Chat\History\MessageStoreInterface;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;

interface NeuronAdapterInterface
{
    public function createAIProvider(): AIProviderInterface;

    public function getMessageStore(): MessageStoreInterface;

    /**
     * @return ToolInterface[]|ToolkitInterface[]
     */
    public function getTools(): array;
}
