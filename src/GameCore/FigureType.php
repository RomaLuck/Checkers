<?php

namespace CheckersOOP\src\gameCore;

abstract class FigureType implements Figure
{
    public string $figure;
    const CHECKER = 'checker';
    const QUEEN = 'queen';

    public function getValue(): string
    {
        return $this->figure;
    }
}