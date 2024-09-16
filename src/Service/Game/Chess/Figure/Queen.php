<?php

namespace App\Service\Game\Chess\Figure;

class Queen implements FigureInterface
{

    public function getId(): array
    {
        return FigureIds::QUEEN;
    }

    public function getFigureRules(): array
    {
        return [];
    }
}