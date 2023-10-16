<?php

namespace App\GameCore;

class Player
{
    public const DIRECTION_UP = 1;
    public const DIRECTION_DOWN = -1;
    public const WHITE = 'white';
    public const BLACK = 'black';
    public string $color;
    public string $oppositeSideColor;
    private string $teamName;
    public int $moveDirection;
    public FigureType $figureType;

    public function __construct(string $teamName)
    {
        $this->teamName = $teamName;
    }

    public function setFigureType(FigureType $figureType): self
    {
        $this->figureType = $figureType;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function move($stepFrom, $stepTo): void
    {
        $this->figureType->move($stepFrom, $stepTo, $this->color, $this->oppositeSideColor, $this->moveDirection);
    }

    /**
     * @return string
     */
    public function getTeamName(): string
    {
        return $this->teamName;
    }
}