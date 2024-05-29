<?php

declare(strict_types=1);

namespace Src\Team;

use Src\Figure\FigureInterface;

class White implements TeamPlayerInterface
{
    public const WHITE_NUMBERS = [1, 3];
    public const DIRECTION_WHITE = 1;
    public const TRANSFORMATION_CELL_WHITE = 7;

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
}