<?php

namespace CheckersOOP\src;

abstract class FigureType
{
    public string $figure;
    const CHECKER = 'checker';
    const QUEEN = 'queen';

    public function getValue(): string
    {
        return $this->figure;
    }

}