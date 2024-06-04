<?php

namespace Src\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Src\Repository\GameRepository;

#[Entity(repositoryClass: GameRepository::class)]
#[Table(name: 'games')]
class Game
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int $id;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'white_user_id', referencedColumnName: 'id')]
    private ?User $white_team_user;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'black_user_id', referencedColumnName: 'id')]
    private ?User $black_team_user;

    #[Column(type: 'string')]
    private string $table;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array<array>
     * @throws \JsonException
     */
    public function getTable(): array
    {
        return json_decode($this->table, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<array> $table
     * @throws \JsonException
     */
    public function setTable(array $table): void
    {
        $this->table = json_encode($table, JSON_THROW_ON_ERROR);
    }

    public function getWhiteTeamUser(): ?User
    {
        return $this->white_team_user;
    }

    public function setWhiteTeamUser(?User $white_team_user): void
    {
        $this->white_team_user = $white_team_user;
    }

    public function getBlackTeamUser(): ?User
    {
        return $this->black_team_user;
    }

    public function setBlackTeamUser(?User $black_team_user): void
    {
        $this->black_team_user = $black_team_user;
    }
}