<?php

declare(strict_types=1);

namespace App\AI;

use NeuronAI\Chat\History\MessageStoreInterface;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;

final class NeuronAdapter implements NeuronAdapterInterface
{
    /**
     * @param iterable<ToolInterface|ToolkitInterface> $tools
     */
    public function __construct(
        private readonly Config $config,
        private readonly MessageStoreInterface $messageStore,
        private readonly iterable $tools,
    ) {
    }

    public function createAIProvider(): AIProviderInterface
    {
        return new Ollama(
            url: $this->config->apiEndpoint,
            model: $this->config->model,
        );
    }

    public function getMessageStore(): MessageStoreInterface
    {
        return $this->messageStore;
    }

    /**
     * @return ToolInterface[]|ToolkitInterface[]
     */
    public function getTools(): array
    {
        return \is_array($this->tools)
            ? $this->tools
            : iterator_to_array($this->tools);
    }
}
