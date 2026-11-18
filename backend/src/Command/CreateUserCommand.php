<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new user (or admin with --admin flag)'
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private UserRepository $users,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Create as admin user (with ROLE_ADMIN)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $isAdmin = $input->getOption('admin');

        if (!is_string($email) || !is_string($password)) {
            $io->error('Email and password must be strings');

            return Command::FAILURE;
        }

        if (empty($email) || empty($password)) {
            $io->error('Email and password cannot be empty');

            return Command::FAILURE;
        }

        $user = $this->users->findByEmail($email);
        if ($user instanceof User) {
            $io->info(sprintf('%s "%s" already exists', $isAdmin ? 'Admin user' : 'User', $email));

            return Command::SUCCESS;
        }

        $user = User::create(
            email: $email,
            password: $password,
            hasher: $this->hasher,
            roles: $isAdmin ? ['ROLE_ADMIN', 'ROLE_USER'] : []
        );

        $this->users->store($user);

        $io->success(sprintf('%s "%s" created successfully!', $isAdmin ? 'Admin user' : 'User', $email));

        return Command::SUCCESS;
    }
}
