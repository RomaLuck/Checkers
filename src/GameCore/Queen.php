<?php

namespace App\GameCore;

class Queen extends FigureType implements Figure
{
    public int $moveOpportunity=100;
    public string $figure = FigureType::QUEEN;

    public function getValue(): string
    {
        return $this->figure;
    }
}