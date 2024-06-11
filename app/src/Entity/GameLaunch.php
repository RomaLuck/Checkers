<?php

namespace Src\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Src\Repository\GameLaunchRepository;

#[Entity(repositoryClass: GameLaunchRepository::class)]
#[Table(name: 'games')]
class GameLaunch
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int $id;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'games')]
    #[ORM\JoinColumn(name: 'white_user_id', referencedColumnName: 'id')]
    private ?User $white_team_user;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'games')]
    #[ORM\JoinColumn(name: 'black_user_id', referencedColumnName: 'id')]
    private ?User $black_team_user;

    #[Column(type: 'json')]
    private array $table_data;

    #[Column(type: 'string')]
    private string $room_id;

    public function __construct()
    {
        $this->setRoomId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTableData(): array
    {
        return $this->table_data;
    }

    public function setTableData(array $table_data): void
    {
        $this->table_data = $table_data;
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

    public function getRoomId(): string
    {
        return $this->room_id;
    }

    public function setRoomId(): void
    {
        $this->room_id = md5((new \DateTime())->getTimestamp());
    }
}