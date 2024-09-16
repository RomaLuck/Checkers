<?php

namespace App\Service\Game\Chess\Team;

use App\Service\Game\Chess\Figure\FigureInterface;

abstract class Team implements TeamInterface
{
    private FigureInterface $figure;

    public function getFigure(): FigureInterface
    {
        return $this->figure;
    }

    public function setFigure(FigureInterface $figure): void
    {
        $this->figure = $figure;
    }
}