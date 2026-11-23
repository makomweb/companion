<?php

declare(strict_types=1);

namespace App\AI;

use NeuronAI\Agent\Agent;
use NeuronAI\Chat\History\MessageStoreInterface;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;

final class Chat extends Agent
{
    public function __construct(private readonly NeuronAdapterInterface $adapter)
    {
        parent::__construct();
    }

    #[\Override]
    protected function provider(): AIProviderInterface
    {
        return $this->adapter->createAIProvider();
    }

    #[\Override]
    protected function messageStore(): MessageStoreInterface
    {
        return $this->adapter->getMessageStore();
    }

    #[\Override]
    protected function instructions(): string
    {
        return 'You are a helpful and concise AI assistant. '
            .'Provide answers in a maximum of 4 sentences. '
            .'Be friendly and direct. '
            .'Format your response as plain, easy-to-understand flowing text.';
    }

    /**
     * @return ToolInterface[]|ToolkitInterface[]
     */
    #[\Override]
    protected function tools(): array
    {
        return $this->adapter->getTools();
    }
}
