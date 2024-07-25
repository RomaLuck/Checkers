<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: UserRepository::class)]
#[Table(name: 'users')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int $id;

    #[Column(type: 'string')]
    #[NotBlank(message: 'Username is required')]
    #[Length(
        min: 8,
        max: 50,
        minMessage: 'Your username must be at least {{ limit }} characters long',
        maxMessage: 'Your username cannot be longer than {{ limit }} characters'
    )]
    private string $username;

    #[Column(type: 'string')]
    #[NotBlank(message: 'Password is required')]
    #[Length(
        min: 8,
        max: 4096,
        minMessage: 'Your password must be at least {{ limit }} characters long',
        maxMessage: 'Your password cannot be longer than {{ limit }} characters'
    )]
    private string $password;

    #[Column]
    private array $roles = [];

    #[OneToMany(targetEntity: GameLaunch::class, mappedBy: 'white_team_user')]
    private Collection $whiteTeamGames;

    #[OneToMany(targetEntity: GameLaunch::class, mappedBy: 'black_team_user')]
    private Collection $blackTeamGames;

    #[OneToMany(targetEntity: GameLaunch::class, mappedBy: 'winner')]
    private Collection $wonGames;

    #[Column(length: 255, nullable: true)]
    private ?string $oauth_id = null;

    public function __construct()
    {
        $this->roles[] = 'ROLE_USER';
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getWhiteTeamGames(): Collection
    {
        return $this->whiteTeamGames;
    }

    public function setWhiteTeamGames(Collection $whiteTeamGames): void
    {
        $this->whiteTeamGames = $whiteTeamGames;
    }

    public function getBlackTeamGames(): Collection
    {
        return $this->blackTeamGames;
    }

    public function setBlackTeamGames(Collection $blackTeamGames): void
    {
        $this->blackTeamGames = $blackTeamGames;
    }

    public function getWonGames(): Collection
    {
        return $this->wonGames;
    }

    public function setWonGames(Collection $wonGames): void
    {
        $this->wonGames = $wonGames;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getOauthId(): ?string
    {
        return $this->oauth_id;
    }

    public function setOauthId(string|int|null $oauth_id): static
    {
        $this->oauth_id = (string) $oauth_id;

        return $this;
    }
}
