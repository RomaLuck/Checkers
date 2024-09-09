<?php

namespace App\Service\Game\Chess\Figure;

class Rook implements FigureInterface
{

    public function getId(): int
    {
        return FigureIds::ROOK;
    }

    public function getFigureRules(): array
    {
        return [];
    }
}