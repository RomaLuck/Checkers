<?php

namespace App\Service\Game\Chess\Figure;

class Bishop implements FigureInterface
{

    public function getId(): array
    {
        return FigureIds::BISHOP;
    }

    public function getFigureRules(): array
    {
        return [];
    }
}