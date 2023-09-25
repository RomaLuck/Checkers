<?php

namespace App\GameCore;

class Checker extends FigureType
{
    public int $moveOpportunity = 1;
    public string $figure = FigureType::CHECKER;

    public function getValue(): string
    {
        return $this->figure;
    }
}