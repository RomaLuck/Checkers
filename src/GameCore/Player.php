<?php

namespace App\GameCore;

class Player
{
    protected const DIRECTION_UP = 1;
    protected const DIRECTION_DOWN = -1;
    protected const WHITE = 'white';
    protected const BLACK = 'black';
    protected string $color;
    protected string $oppositeSideColor;
    private string $teamName;
    protected int $moveDirection;
    private FigureType $figureType;

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