<?php

declare(strict_types=1);

namespace App\Service\Game\Team;

use App\Service\Game\Figure\FigureInterface;

final class Black implements PlayerInterface
{
    public const BLACK_NUMBERS = [2, 4];
    public const DIRECTION_BLACK = -1;
    public const TRANSFORMATION_CELL_BLACK = 0;

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
        return self::BLACK_NUMBERS;
    }

    public function getDirection(): int
    {
        return self::DIRECTION_BLACK;
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

    public static function isBlackNumber(int $number): bool
    {
        return in_array($number, self::BLACK_NUMBERS);
    }
}
