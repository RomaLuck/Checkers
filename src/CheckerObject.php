<?php

namespace CheckersOOP\src;

use CheckersOOP\db\DbObject;
use Exception;

class CheckerObject
{
    public string $chooseFigure;
    public string $setStep;
    public DbObject $object;
    public Player $player;
    public array $figures;
    public Figure $figure;

    public function __construct(DbObject $object, Player $player)
    {
        $this->object = $object;
        $this->player = $player;
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
        $this->figures[] = $this->figure;
        return $this->figure;
    }

    /**
     * @throws Exception
     */
    public function move($chooseFigure, $setStep): void
    {
        $this->chooseFigure = $chooseFigure;
        $this->setStep = $setStep;
        if ($this->checkForMove()) {
            $this->object->updateItems(['team' => $this->player->color, 'figure' => $this->figure->getValue()], ['id' => $this->setStep]);
            $this->object->updateItems(['team' => '', 'figure' => ''], ['id' => $this->chooseFigure]);
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
        if (in_array($this->chooseFigure, $this->object->showItems('id', ['team' => $this->player->color]))) {
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
            in_array($this->chooseFigure, $this->object->showAllItems('id')) and
            in_array($this->setStep, $this->object->showAllItems('id'))
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
        if ($this->object->showItem('team', ['id' => $this->setStep]) === '') {
            return true;
        }
        throw new Exception("step for move is false");
    }

    /**
     * @throws Exception
     */
    public function hasOpportunity(): bool
    {
        if ($this->figure->moveOpportunity >= $this->defineMoveStep() * $this->player->moveDirection &&
            $this->defineMoveStep() !== 0) {
            return true;
        }
        throw new Exception("you don't have such opportunity");
    }

    /**
     * @throws Exception
     */
    public function hasTrueDirection(): bool
    {
        if ($this->defineMoveStep() === $this->player->moveDirection) {
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
//         $this->object->showItem($this->setStep)['Team'] !== $this->side
//         and $this->object->showItem($this->setStep)['Team'] !== Null
//     ) {
//         return true;
//     }
//     return false;
// }