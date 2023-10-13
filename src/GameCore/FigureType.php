<?php

namespace App\GameCore;

use App\Db\CheckerObjectRepository;
use Exception;

class FigureType
{
    public const CHECKER = 'checker';
    public const QUEEN = 'queen';
    public string $stepFrom;
    public string $stepTo;
    public int $moveOpportunity;
    public string $figureName;

    public CheckerObjectRepository $repository;

    public function __construct(CheckerObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     */
    public function move($stepFrom, $stepTo, $playerColor, $oppositePlayerColor, $moveDirection): void
    {
        $this->stepFrom = $stepFrom;
        $this->stepTo = $stepTo;
        if ($this->checkForAttack($playerColor, $oppositePlayerColor, $moveDirection)) {
            $this->attackOppositePlayer($playerColor);
        } else if ($this->checkForMove($playerColor, $moveDirection)) {
            $this->walk($playerColor, $oppositePlayerColor);
        } else {
            throw new \RuntimeException("Figure can't be moved");
        }
    }

    /**
     * @throws Exception
     */
    public function checkForMove($playerColor, $moveDirection): bool
    {
        return $this->isCheckerInDesk()
            && $this->isCheckerInTeam($playerColor)
            && $this->isStepForMove()
            && $this->hasOpportunity($moveDirection)
            && $this->hasTrueDirection($moveDirection)
            && $this->isStepOnArea();
    }

    /**
     * @throws Exception
     */
    public function checkForAttack($playerColor, $oppositePlayerColor, $moveDirection): bool
    {
        return $this->isCheckerInDesk()
            && $this->isCheckerInTeam($playerColor)
            && $this->hasOpportunity($moveDirection)
            && $this->isStepOnArea()
            && $this->isStepForAttack($oppositePlayerColor)
            && $this->isCellAfterAttackAvailable()
            && $this->isStepAfterAttackOnDesk();
    }

    /**
     * @throws Exception
     */
    public function hasOpportunity($moveDirection): bool
    {
        return $this->moveOpportunity >= $this->defineMoveStep() * $moveDirection
            && $this->defineMoveStep() !== 0;
    }

    /**
     * @throws Exception
     */
    public function hasTrueDirection($moveDirection): bool
    {
        return $this->defineMoveStep() === $moveDirection;
    }

    public function defineMoveStep(): int
    {
        return ((int)$this->getSplitPiece($this->stepTo, 1) - (int)$this->getSplitPiece($this->stepFrom, 1));
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
        return in_array($this->stepTo, $this->repository->getAreaForWalk($this->stepFrom, $this->moveOpportunity));
    }

    public function isCheckerInDesk(): bool
    {
        return $this->repository->isCheckerInDesk($this->stepFrom, $this->stepTo);
    }

    public function isCheckerInTeam($playerColor): bool
    {
        return $this->repository->isCheckerInTeam($this->stepFrom, $playerColor);
    }

    public function isStepForMove(): bool
    {
        return $this->repository->isStepForMove($this->stepTo);
    }

    public function isStepForAttack($oppositePlayerColor): bool
    {
        return $this->repository->isStepForAttack($this->stepTo, $oppositePlayerColor);
    }

    public function isCellAfterAttackAvailable(): bool
    {
        return $this->repository->isCellAfterAttackAvailable($this->stepFrom, $this->stepTo);
    }

    public function isStepAfterAttackOnDesk(): bool
    {
        return $this->repository->isStepAfterAttackOnDesk($this->stepFrom, $this->stepTo);
    }

    public function getFigureName(): string
    {
        return $this->figureName;
    }

    public function attackOppositePlayer($playerColor): void
    {
        $this->repository->attackOppositePlayer($this->stepFrom, $this->stepTo, $playerColor, $this->getFigureName());
    }

    public function walk($playerColor, $oppositePlayerColor): void
    {
        $this->repository->walk($this->stepFrom, $this->stepTo, $playerColor, $oppositePlayerColor);
    }
}