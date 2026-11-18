<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password = '';

    /**
     * @var string[]
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /**
     * @param non-empty-string $email
     * @param string[]         $roles
     */
    protected function __construct(string $email, array $roles)
    {
        $this->email = $email;
        $this->roles = $roles;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @param non-empty-string $email
     * @param non-empty-string $password
     * @param string[]         $roles
     */
    public static function create(string $email, string $password, UserPasswordHasherInterface $hasher, array $roles = []): self
    {
        $user = new self($email, $roles);
        $user->password = $hasher->hashPassword($user, $password);

        return $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return non-empty-string
     */
    public function getEmail(): string
    {
        assert(!empty($this->email));

        return $this->email;
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        assert(!empty($this->email));

        return $this->email;
    }

    /**
     * @return non-empty-string
     */
    public function getPassword(): string
    {
        assert(!empty($this->password));

        return $this->password;
    }

    public function setPassword(string $password): self
    {
        if (empty($password)) {
            throw new \Exception('Argument $password should not be empty!');
        }

        $this->password = $password;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return array_unique([...$this->roles, 'ROLE_USER']);
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function eraseCredentials(): void
    {
    }
}
