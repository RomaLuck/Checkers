<?php

namespace CheckersOOP\src;

use CheckersOOP\db\DbObject;
use Exception;

class CheckerObject
{
    public string $chooseFigure;
    public string $setStep;
    private DbObject $object;
    private Player $player;
    private Figure $figure;
    private CheckerDesk $desk;

    public function __construct(DbObject $object, Player $player, CheckerDesk $desk)
    {
        $this->object = $object;
        $this->player = $player;
        $this->desk = $desk;
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
    public function move($chooseFigure, $setStep): void
    {
        $this->chooseFigure = $chooseFigure;
        $this->setStep = $setStep;
        if ($this->checkForAttack()) {
            $this->object->updateItems(['team' => $this->player->color, 'figure' => $this->figure->getValue()], ['id' => $this->getFuturePositionAfterBeat()]);
            $this->object->updateItems(['team' => '', 'figure' => ''], ['id' => $this->chooseFigure]);
            $this->object->updateItems(['team' => '', 'figure' => ''], ['id' => $this->setStep]);
        } else if ($this->checkForMove()) {
            $this->object->updateItems(['team' => $this->player->color, 'figure' => $this->figure->getValue()], ['id' => $this->setStep]);
            $this->object->updateItems(['team' => '', 'figure' => ''], ['id' => $this->chooseFigure]);
        } else {
            throw new Exception("Figure can't be moved");
        }
    }

    /**
     * @throws Exception
     */
    public function checkForMove(): bool
    {
        return $this->isCheckerInDesk()
            && $this->isCheckerInTeam()
            && $this->isStepForMove()
            && $this->hasOpportunity()
            && $this->hasTrueDirection()
            && $this->isStepOnArea();
    }

    /**
     * @throws Exception
     */
    public function checkForAttack(): bool
    {
        return $this->isCheckerInDesk()
            && $this->isCheckerInTeam()
            && $this->hasOpportunity()
            && $this->isStepOnArea()
            && $this->isStepForAttack()
            && $this->object->showItem('team', ['id' => $this->getFuturePositionAfterBeat()]) === ''
            && $this->isStepAfterAttackOnDesk();
    }

    /**
     * @throws Exception
     */
    public function isCheckerInTeam(): bool
    {
        if (in_array($this->chooseFigure, $this->object->showItems('id', ['team' => $this->player->color]))) {
            return true;
        }
        throw new Exception("Figure isn't in team");
    }


    /**
     * @throws Exception
     */
    public function isCheckerInDesk(): bool
    {
        if (
            in_array($this->chooseFigure, $this->object->showAllItems('id'))
            && in_array($this->setStep, $this->object->showAllItems('id'))
        ) {
            return true;
        }
        throw new Exception("Figure isn't in desk");
    }

    /**
     * @throws Exception
     */
    public function isStepForMove(): bool
    {
        if ($this->object->showItem('team', ['id' => $this->setStep]) === '') {
            return true;
        }
        throw new Exception("Step for move is false");
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
        throw new Exception("You don't have such opportunity");
    }

    /**
     * @throws Exception
     */
    public function hasTrueDirection(): bool
    {
        if ($this->defineMoveStep() == $this->player->moveDirection) {
            return true;
        }
        throw new Exception("False direction");
    }

    /**
     * @throws Exception
     */
    public function isStepOnArea(): bool
    {
        if (in_array($this->setStep, $this->getAreaForWalk())) {
            return true;
        }
        throw new Exception("Set step is not on area");
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
    public function getAreaForWalk(): array
    {
        $right = $this->desk->horizontalSideDesk[$this->getPositionOnDesk($this->chooseFigure, $this->desk->horizontalSideDesk)
        + $this->figure->moveOpportunity];

        $left = $this->desk->horizontalSideDesk[$this->getPositionOnDesk($this->chooseFigure, $this->desk->horizontalSideDesk)
        - $this->figure->moveOpportunity];

        $forward = $this->desk->verticalSideDesk[$this->getPositionOnDesk($this->chooseFigure, $this->desk->verticalSideDesk)
        + $this->figure->moveOpportunity];

        $back = $this->desk->verticalSideDesk[$this->getPositionOnDesk($this->chooseFigure, $this->desk->verticalSideDesk)
        - $this->figure->moveOpportunity];

        return array_intersect([$right . $forward, $left . $forward, $right . $back, $left . $back],
            $this->object->showAllItems('id'));
    }



    public function isStepForAttack(): bool
    {
        if ($this->object->showItem('team', ['id' => $this->setStep]) === $this->player->oppositeSide) {
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function isStepAfterAttackOnDesk():bool
    {
        if (in_array($this->getFuturePositionAfterBeat(),$this->object->showAllItems('id'))){
            return true;
        }
        throw new Exception("Step after attack is out of the board");
    }

    /**
     * @throws Exception
     */
    public function getPositionOnDesk($piece, $sideOfDesk): int
    {
        $idPiece = match ($sideOfDesk) {
            $this->desk->verticalSideDesk => 1,
            $this->desk->horizontalSideDesk => 0,
            default => throw new Exception('Wrong number piece\'s position'),
        };
        return array_search($this->getSplitPiece($piece, $idPiece), $sideOfDesk);
    }

    /**
     * @throws Exception
     */
    public function getFuturePositionAfterBeat(): string
    {
        $horizontalSide = $this->desk->horizontalSideDesk[($this->getPositionOnDesk($this->setStep, $this->desk->horizontalSideDesk)
            - $this->getPositionOnDesk($this->chooseFigure, $this->desk->horizontalSideDesk)) * 2
        + $this->getPositionOnDesk($this->chooseFigure, $this->desk->horizontalSideDesk)];

        $verticalSide = $this->desk->verticalSideDesk[($this->getPositionOnDesk($this->setStep, $this->desk->verticalSideDesk)
            - $this->getPositionOnDesk($this->chooseFigure, $this->desk->verticalSideDesk)) * 2
        + $this->getPositionOnDesk($this->chooseFigure, $this->desk->verticalSideDesk)];

        return $horizontalSide . $verticalSide;
    }
}


