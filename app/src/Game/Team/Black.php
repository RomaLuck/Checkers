<?php

declare(strict_types=1);

namespace Src\Game\Team;

use Src\Game\Figure\FigureInterface;

final class Black implements PlayerInterface
{
    public const BLACK_NUMBERS = [2, 4];
    public const DIRECTION_BLACK = -1;
    public const TRANSFORMATION_CELL_BLACK = 0;

    private string $name;
    private FigureInterface $figure;

    public function __construct(string $name)
    {
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
}