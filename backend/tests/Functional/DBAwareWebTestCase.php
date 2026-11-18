<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** Base class for database-aware functional tests. */
abstract class DBAwareWebTestCase extends WebTestCase
{
    protected const TEST_EMAIL = 'test@example.com';
    protected const TEST_PASSWORD = 'password123';

    protected const ADMIN_EMAIL = 'admin@example.com';
    protected const ADMIN_PASSWORD = 'admin123';

    protected static ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        parent::setUp();

        self::ensureKernelShutdown();
        self::$client = self::createClient();

        $em = self::getContainer()->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);

        $schemaTool = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        if ([] === $metadata) {
            return;
        }

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $em->clear();

        self::createTestUser();
    }

    protected static function getKernelBrowser(): KernelBrowser
    {
        assert(self::$client instanceof KernelBrowser);

        return self::$client;
    }

    /**
     * Create a test user and persist to database.
     *
     * @param non-empty-string $email
     * @param non-empty-string $password
     * @param string[]         $roles
     */
    protected static function createTestUser(
        string $email = self::TEST_EMAIL,
        string $password = self::TEST_PASSWORD,
        array $roles = [],
    ): User {
        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        assert($hasher instanceof UserPasswordHasherInterface);

        $user = User::create(
            email: $email,
            password: $password,
            hasher: $hasher,
            roles: $roles
        );

        $users = self::getContainer()->get(UserRepository::class);
        assert($users instanceof UserRepository);

        $users->store($user);

        return $user;
    }
}
