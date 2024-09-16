<?php

declare(strict_types=1);

namespace App\Service\Game\Checkers\Team;

use App\Service\Game\Checkers\Figure\FigureInterface;

interface PlayerInterface
{
    public function getId(): int;

    public function getName(): string;

    /**
     * @return array<int>
     */
    public function getTeamNumbers(): array;

    public function getDirection(): int;

    public function setFigure(FigureInterface $figure): void;

    public function getFigure(): FigureInterface;

    public function isTurnForPlayer(bool $currentTurn): bool;
}
