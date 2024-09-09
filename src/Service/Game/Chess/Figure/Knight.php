<?php

namespace App\Service\Game\Chess\Figure;

class Knight implements FigureInterface
{

    public function getId(): int
    {
        return FigureIds::KNIGHT;
    }

    public function getFigureRules(): array
    {
        return [];
    }
}