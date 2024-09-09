<?php

namespace App\Service\Game\Chess\Figure;

class King implements FigureInterface
{

    public function getId(): int
    {
        return FigureIds::KING;
    }

    public function getFigureRules(): array
    {
        return [];
    }
}