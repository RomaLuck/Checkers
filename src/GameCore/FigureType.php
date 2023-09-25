<?php

namespace App\GameCore;

abstract class FigureType
{
    public string $figure;
    const CHECKER = 'checker';
    const QUEEN = 'queen';

    abstract public function getValue(): string;
}