<?php

declare(strict_types=1);

namespace Src\Team;

use Src\Figure\FigureInterface;

interface PlayerInterface
{
    public function getName(): string;

    public function getTeamNumbers(): array;

    public function getDirection(): int;

    public function setFigure(FigureInterface $figure): void;

    public function getFigure(): FigureInterface;
}