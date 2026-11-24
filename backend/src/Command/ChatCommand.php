<?php

declare(strict_types=1);

namespace App\Command;

use App\AI\Chat;
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
    private const EXIT_COMMANDS = ['exit', 'quit', 'q', 'bye'];

    public function __construct(private readonly Chat $chat)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $exitOptions = implode('", "', self::EXIT_COMMANDS);
        $io->note(sprintf('Type "%s" to end the conversation.', $exitOptions));

        $threadId = self::createChatId();
        $reportAdapter = self::createReportAdapter($io);

        do {
            $question = $this->ask($input, $output);
            $this->answer($question, $threadId, $reportAdapter);
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

    private function answer(string $question, string $threadId, ReportInterface $report): void
    {
        if (empty($question)) {
            return;
        }

        $report->start('🤖 Agent: ');

        /** @var non-empty-string $threadId */
        $stream = $this->chat->stream($question, $threadId);

        foreach ($stream as $chunk) {
            $report->write($chunk);
        }

        $report->finish();
    }

    private static function createChatId(): string
    {
        return Uuid::v4()->toRfc4122();
    }

    private static function createReportAdapter(SymfonyStyle $io): ReportInterface
    {
        return new class($io) implements ReportInterface {
            public function __construct(private SymfonyStyle $io)
            {
            }

            public function start(string $message): void
            {
                $this->io->write($message);
            }

            public function write(string $chunk): void
            {
                $this->io->write($chunk);
            }

            public function finish(): void
            {
                $this->io->newLine();
            }
        };
    }

    private static function isExitCommand(string $input): bool
    {
        return \in_array(strtolower(trim($input)), self::EXIT_COMMANDS, strict: true);
    }
}
