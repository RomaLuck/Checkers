<?php

namespace App\GameCore;

use App\Db\CheckerObjectRepository;
use App\Db\CheckerObjectRepository;
use App\Db\DbObject;
use CheckersOOP\src\gameCore\Checker;
use CheckersOOP\src\gameCore\Figure;
use CheckersOOP\src\gameCore\FigureType;
use CheckersOOP\src\gameCore\Queen;
use Exception;

abstract class Player
{
    public const DIRECTION_UP = 1;
    public const DIRECTION_DOWN = -1;
    public const WHITE = 'white';
    public const BLACK = 'black';
    public string $teamName;
    public string $color;
    public int $moveDirection;
    public string $oppositeSide;
    public string $chooseFigure;
    public string $setStep;
    public CheckerObjectRepository $desk;
    public CheckerObjectRepository $checker;
    private Queen|Checker $figure;

    public function __construct(CheckerObjectRepository $desk, CheckerObjectRepository $checker)
    {
        $this->desk = $desk;
        $this->checker = $checker;
    }

    /**
     * @throws Exception
     */
    public function createFigure($figureType): Figure
    {
        $this->figure = match ($figureType) {
            FigureType::CHECKER => new Checker(),
            FigureType::QUEEN => new Queen(),
            default => throw new Exception("Invalid figure type: " . $figureType),
        };
        return $this->figure;
    }

    /**
     * @throws Exception
     */
    public function checkForMove(): bool
    {
        return $this->checker->isCheckerInDesk()
            && $this->checker->isCheckerInTeam()
            && $this->checker->isStepForMove()
            && $this->hasOpportunity()
            && $this->hasTrueDirection()
            && $this->isStepOnArea();
    }

    /**
     * @throws Exception
     */
    public function checkForAttack(): bool
    {
        return $this->checker->isCheckerInDesk()
            && $this->checker->isCheckerInTeam()
            && $this->hasOpportunity()
            && $this->isStepOnArea()
            && $this->checker->isStepForAttack()
            && $this->checker->isCellAfterAttackAvailable()
            && $this->checker->isStepAfterAttackOnDesk();
    }

    /**
     * @throws Exception
     */
    public function move($chooseFigure, $setStep): void
    {
        $this->chooseFigure = $chooseFigure;
        $this->setStep = $setStep;
        if ($this->checkForAttack()) {
            $this->checker->attackOppositePlayer();
        } else if ($this->checkForMove()) {
            $this->checker->walk();
        } else {
            throw new Exception("Figure can't be moved");
        }
    }

    /**
     * @throws Exception
     */
    public function hasOpportunity(): bool
    {
        return $this->figure->moveOpportunity >= $this->defineMoveStep() * $this->moveDirection
            && $this->defineMoveStep() !== 0;
    }

    /**
     * @throws Exception
     */
    public function hasTrueDirection(): bool
    {
        return $this->defineMoveStep() === $this->moveDirection;
    }

    public function defineMoveStep(): int
    {
        return ((int)$this->getSplitPiece($this->setStep, 1) - (int)$this->getSplitPiece($this->chooseFigure, 1));
    }

    public function getSplitPiece(string $data, int $key): string
    {
        return (str_split($data))[$key];
    }

    /**
     * @throws Exception
     */
    public function isStepOnArea(): bool
    {
        return in_array($this->setStep, $this->checker->getAreaForWalk());
    }
}