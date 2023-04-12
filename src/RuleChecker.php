<?php

namespace CheckersOOP\src;

use CheckersOOP\src\Player;
use CheckersOOP\db\DbObject;
use CheckersOOP\src\CheckerDesk;

class RuleChecker
{
    private $player;
    private $checkerDesk;

    public function __construct(Player $player, CheckerDesk $checkerDesk)
    {
        $this->player = $player;
        $this->checkerDesk = $checkerDesk;
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
        if (in_array($this->player->chooseFigure, $this->player->showItems('pieceName'))) {
            return true;
        }
        return false;
    }


    public function isCheckerInDesk(): bool
    {
        if (
            in_array($this->player->chooseFigure, $this->checkerDesk->showItems('id')) and
            in_array($this->player->setStep, $this->checkerDesk->showItems('id'))
        ) {
            return true;
        }
        return false;
    }

    public function isStepForAttack()
    {
        if (
            $this->checkerDesk->showItem($this->player->setStep)['Team'] !== $this->player->teamName
            and $this->checkerDesk->showItem($this->player->setStep)['Team'] !== Null
        ) {
            return true;
        }
        return false;
    }

    public function isStepForMove()
    {
        if ($this->checkerDesk->showItem($this->player->setStep)['Team'] === Null) {
            return true;
        }
        return false;
    }

    public function hasOpportunity()
    {
        if ($this->player->moveOpportunity === $this->defineMoveStep() * (int)$this->player->moveDirection) {
            return true;
        }
        return false;
    }

    public function hasTrueDirection()
    {
        if ($this->defineMoveStep() === $this->player->moveDirection) {
            return true;
        }
        return false;
    }

    public function defineMoveStep()
    {
        return (int)((str_split((string)$this->player->setStep))[1] - str_split((string)$this->player->chooseFigure)[1]);
    }
}
