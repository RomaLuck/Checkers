<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\GameLaunchRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity(repositoryClass: GameLaunchRepository::class)]
#[Table(name: 'games')]
class GameLaunch
{
    public const WHITE_TURN = true;

    public const BLACK_TURN = false;

    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int $id;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'whiteTeamGames')]
    #[ORM\JoinColumn(name: 'white_user_id', referencedColumnName: 'id')]
    private ?User $white_team_user = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'blackTeamGames')]
    #[ORM\JoinColumn(name: 'black_user_id', referencedColumnName: 'id')]
    private ?User $black_team_user = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'wonGames')]
    #[ORM\JoinColumn(name: 'winner_id', referencedColumnName: 'id')]
    private ?User $winner;

    #[Column(type: 'json')]
    private array $table_data;

    #[Column(type: 'string')]
    private string $room_id;

    #[Column(type: 'boolean')]
    private bool $is_active;

    #[Column(type: 'smallint')]
    private ?int $strategy_id = null;

    #[Column(type: 'boolean')]
    private bool $currentTurn = self::WHITE_TURN;

    #[Column(type: 'smallint', nullable: true)]
    private ?int $complexity = null;

    #[Column]
    private ?int $type_id = null;

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

    public function setWhiteTeamUser(?UserInterface $white_team_user): void
    {
        $this->white_team_user = $white_team_user;
    }

    public function getBlackTeamUser(): ?User
    {
        return $this->black_team_user;
    }

    public function setBlackTeamUser(?UserInterface $black_team_user): void
    {
        $this->black_team_user = $black_team_user;
    }

    public function getRoomId(): string
    {
        return $this->room_id;
    }

    public function setRoomId(): void
    {
        $this->room_id = md5((string) (new \DateTime())->getTimestamp());
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function getWinner(): ?User
    {
        return $this->winner;
    }

    public function setWinner(?User $winner): void
    {
        $this->winner = $winner;
    }

    public function getStrategyId(): ?int
    {
        return $this->strategy_id;
    }

    public function setStrategyId(int $strategy_id): static
    {
        $this->strategy_id = $strategy_id;

        return $this;
    }

    public function getCurrentTurn(): bool
    {
        return $this->currentTurn;
    }

    public function setCurrentTurn(bool $currentTurn): void
    {
        $this->currentTurn = $currentTurn;
    }

    public function assignPlayerToGame(UserInterface $player): bool
    {
        if (!$this->getWhiteTeamUser()) {
            $this->setWhiteTeamUser($player);

            return true;
        }
        if (!$this->getBlackTeamUser()) {
            $this->setBlackTeamUser($player);

            return true;
        }

        return false;
    }

    public function getComplexity(): ?int
    {
        return $this->complexity;
    }

    public function setComplexity(?int $complexity): void
    {
        $this->complexity = $complexity;
    }

    public function getTypeId(): ?int
    {
        return $this->type_id;
    }

    public function setTypeId(int $type_id): static
    {
        $this->type_id = $type_id;

        return $this;
    }
}
