<?php

namespace App\Service\Game\Chess\Team;

use App\Service\Game\Chess\Figure\FigureInterface;

interface TeamInterface
{
    /**
     * @return array<int>
     */
    public function getTeamNumbers(): array;

    public function getFigure(): FigureInterface;

    public function setFigure(FigureInterface $figure);

    public function isTurnForTeam(bool $turn): bool;
}
