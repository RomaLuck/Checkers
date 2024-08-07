<?php

declare(strict_types=1);

namespace App\Service\Game\Team;

use App\Entity\GameLaunch;
use App\Service\Game\Figure\FigureInterface;

final class White implements PlayerInterface
{
    public const WHITE_NUMBERS = [1, 3];
    public const DIRECTION_WHITE = 1;
    public const TRANSFORMATION_CELL_WHITE = 7;

    private string $name;
    private FigureInterface $figure;
    private int $id;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTeamNumbers(): array
    {
        return self::WHITE_NUMBERS;
    }

    public function getDirection(): int
    {
        return self::DIRECTION_WHITE;
    }

    public function setFigure(FigureInterface $figure): void
    {
        $this->figure = $figure;
    }

    public function getFigure(): FigureInterface
    {
        return $this->figure;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function isWhiteNumber(int $number): bool
    {
        return in_array($number, self::WHITE_NUMBERS);
    }

    public function isTurnForPlayer(bool $currentTurn): bool
    {
        return $currentTurn === GameLaunch::WHITE_TURN;
    }
}
