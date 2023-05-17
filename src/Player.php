<?php

namespace CheckersOOP\src;

abstract class Player
{
    const DIRECTION_UP = 1;
    const DIRECTION_DOWN = -1;
    const WHITE = 'white';
    const BLACK = 'black';
    public string $teamName;
}