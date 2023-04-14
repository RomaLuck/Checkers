<?php

namespace CheckersOOP\src;

use CheckersOOP\src\BlackTeam;
use CheckersOOP\src\WhiteTeam;
use CheckersOOP\src\CheckerDesk;

class CheckerObject
{
    public $chooseFigure;
    public $setStep;
    public $teamName;
    public $checkerDesk;
    public $moveOpportunity;
    public $moveDirection;
    public $side;

    public function __construct(CheckerDesk $checkerDesk)
    {
        $this->checkerDesk = $checkerDesk;
    }

    public function createTeam($teamName, $side)
    {
        switch ($side) {
            case 'white':
                return new WhiteTeam($this->checkerDesk, $teamName);
                break;
            case 'black':
                return new BlackTeam($this->checkerDesk, $teamName);
                break;
            default:
                throw new \InvalidArgumentException("Invalid side name: $side");
                break;
        }
    }

    public function move($chooseFigure, $setStep)
    {
        $this->chooseFigure = $chooseFigure;
        $this->setStep = $setStep;
        if ($this->checkForMove()) {
            $this->checkerDesk->updateItems('team', $this->side, 'id', $this->setStep);
            $this->checkerDesk->updateItems('team', '', 'id', $this->chooseFigure);
        } else {
            echo 'you can not move this item';
        }
    }

    public function checkForMove(): bool
    {
        return $this->isCheckerInTeam()
            && $this->isStepForMove()
            && $this->isCheckerInDesk()
            && $this->hasOpportunity()
            && $this->hasTrueDirection();
    }

    public function isCheckerInTeam(): bool
    {
        if (in_array($this->chooseFigure, $this->checkerDesk->showItems('id', 'team', $this->side))) {
            return true;
        }
        return false;
    }


    public function isCheckerInDesk(): bool
    {
        if (
            in_array($this->chooseFigure, $this->checkerDesk->showAllItems('id')) and
            in_array($this->setStep, $this->checkerDesk->showAllItems('id'))
        ) {
            return true;
        }
        return false;
    }

    public function isStepForMove()
    {
        if ($this->checkerDesk->showItem('team', 'id', $this->setStep) === '') {
            return true;
        }
        return false;
    }

    public function hasOpportunity()
    {
        if ($this->moveOpportunity === $this->defineMoveStep() * (int)$this->moveDirection) {
            return true;
        }
        return false;
    }

    public function hasTrueDirection()
    {
        if ($this->defineMoveStep() === $this->moveDirection) {
            return true;
        }
        return false;
    }

    public function defineMoveStep()
    {
        return (int)((str_split((string)$this->setStep))[1] - str_split((string)$this->chooseFigure)[1]);
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