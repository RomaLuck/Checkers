<?php

namespace CheckersOOP\src;

use Exception;

class CheckerObject
{
    public string $chooseFigure;
    public string $setStep;
    public string $teamName;
    public CheckerDesk $checkerDesk;
    public int $moveOpportunity;
    public int $moveDirection;
    public string $side;

    public function __construct(CheckerDesk $checkerDesk)
    {
        $this->checkerDesk = $checkerDesk;
    }

    /**
     * @throws Exception
     */
    public function createFigure($figure): Checker|Queen
    {
        return match ($figure) {
            'checker' => new Checker($this->checkerDesk),
            'queen' => new Queen($this->checkerDesk),
            default => throw new Exception("Invalid figure name: $figure"),
        };
    }

    /**
     * @throws Exception
     */
    public function move($chooseFigure, $setStep): void
    {
        $this->chooseFigure = $chooseFigure;
        $this->setStep = $setStep;
        if ($this->checkForMove()) {
            $this->checkerDesk->updateItems('team', $this->side, 'id', $this->setStep);
            $this->checkerDesk->updateItems('team', '', 'id', $this->chooseFigure);
        } else {
            throw new Exception("the figure can't be moved");
        }
    }

    /**
     * @throws Exception
     */
    public function checkForMove(): bool
    {
        return $this->isCheckerInTeam()
            && $this->isStepForMove()
            && $this->isCheckerInDesk()
            && $this->hasOpportunity()
            && $this->hasTrueDirection();
    }

    /**
     * @throws Exception
     */
    public function isCheckerInTeam(): bool
    {
        if (in_array($this->chooseFigure, $this->checkerDesk->showItems('id', 'team', $this->side))) {
            return true;
        }
        throw new Exception("the figure isn't in team");
    }


    /**
     * @throws Exception
     */
    public function isCheckerInDesk(): bool
    {
        if (
            in_array($this->chooseFigure, $this->checkerDesk->showAllItems('id')) and
            in_array($this->setStep, $this->checkerDesk->showAllItems('id'))
        ) {
            return true;
        }
        throw new Exception("the figure isn't in desk");
    }

    /**
     * @throws Exception
     */
    public function isStepForMove(): bool
    {
        if ($this->checkerDesk->showItem('team', 'id', $this->setStep) === '') {
            return true;
        }
        throw new Exception("step for move is false");
    }

    /**
     * @throws Exception
     */
    public function hasOpportunity() : bool
    {
        if ($this->moveOpportunity === $this->defineMoveStep() * $this->moveDirection) {
            return true;
        }
        throw new Exception("you don't have such opportunity");
    }

    /**
     * @throws Exception
     */
    public function hasTrueDirection(): bool
    {
        if ($this->defineMoveStep() === $this->moveDirection) {
            return true;
        }
        throw new Exception("false direction");
    }

    public function defineMoveStep(): int
    {
        return (int)((str_split($this->setStep))[1] - str_split($this->chooseFigure)[1]);
    }
}


    // public function isStepForAttack()
    // {
    //     if (
    //         $this->checkerDesk->showItem($this->setStep)['Team'] !== $this->side
    //         and $this->checkerDesk->showItem($this->setStep)['Team'] !== Null
    //     ) {
    //         return true;
    //     }
    //     return false;
    // }