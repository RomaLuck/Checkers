<?php

namespace Src\Team;

use Src\Figure\FigureInterface;

interface TeamPlayerInterface
{
    public function getName();

    public function getTeamNumbers(): array;

    public function getDirection(): int;

    public function setFigure(FigureInterface $figure): void;

    public function getFigure(): FigureInterface;
}