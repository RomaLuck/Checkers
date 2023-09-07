<?php

namespace CheckersOOP\src\gameCore;

class Checker extends FigureType implements Figure
{
    public int $moveOpportunity = 1;
    public string $figure = FigureType::CHECKER;

    public function getValue(): string
    {
        return $this->figure;
    }
}