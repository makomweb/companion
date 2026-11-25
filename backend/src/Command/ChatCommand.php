<?php

declare(strict_types=1);

namespace App\Command;

use App\AI\ObservableChat;
use NeuronAI\Chat\Messages\UserMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:chat',
    description: 'Run an interactive chat with the AI agent',
)]
class ChatCommand extends Command
{
    public function __construct(private readonly ObservableChat $chat)
    {
        parent::__construct();

        $this->chat->setThreadId(self::createChatId());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->note('Type "exit" or "quit" to end the conversation.');

        $this->chat->subscribe(new ChatEventHandler($io));

        do {
            $question = $this->ask($input, $output);
            $this->answer($question);
        } while (!self::isExitCommand($question));

        return Command::SUCCESS;
    }

    private function ask(InputInterface $input, OutputInterface $output): string
    {
        $question = new Question('🤓 You: ');
        $question->setTrimmable(true);

        $helper = $this->getHelper('question');
        assert($helper instanceof QuestionHelper);
        /** @var string|null $userInput */
        $userInput = $helper->ask($input, $output, $question);

        return (string) ($userInput ?? '');
    }

    private function answer(string $question): void
    {
        if (!empty($question)) {
            $this->chat->submit(new UserMessage($question));
        }
    }

    private static function createChatId(): string
    {
        return Uuid::v4()->toRfc4122();
    }

    private static function isExitCommand(string $input): bool
    {
        $exit_commands = ['exit', 'quit', 'q', 'bye'];

        return \in_array(strtolower(trim($input)), $exit_commands, true);
    }
}
