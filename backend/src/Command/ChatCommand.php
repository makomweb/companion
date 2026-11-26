<?php

declare(strict_types=1);

namespace App\Command;

use App\AI\ObservableChat;
use Symfony\AI\Platform\Message\Message;
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
    private const EXIT_COMMANDS = ['exit', 'quit'];

    public function __construct(private readonly ObservableChat $chat)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->note(sprintf('Type "%s" or "%s" to end the conversation.', self::EXIT_COMMANDS[0], self::EXIT_COMMANDS[1]));

        $this->chat->subscribe(new ChatEventHandler($io));

        $threadId = self::createChatId();
        do {
            $question = $this->ask($input, $output);
            $this->answer($question, $threadId);
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

    /** @param non-empty-string $threadId */
    private function answer(string $question, string $threadId): void
    {
        if (!empty($question)) {
            $this->chat->submit(Message::ofUser($question), $threadId);
        }
    }

    /** @return non-empty-string */
    private static function createChatId(): string
    {
        return Uuid::v4()->toRfc4122();
    }

    private static function isExitCommand(string $input): bool
    {
        return \in_array(strtolower(trim($input)), self::EXIT_COMMANDS, strict: true);
    }
}
