<?php

declare(strict_types=1);

namespace App\Service\Game\Team;

use App\Service\Game\Figure\FigureInterface;

interface PlayerInterface
{
    public function getId(): int;

    public function getName(): string;

    public function getTeamNumbers(): array;

    public function getDirection(): int;

    public function setFigure(FigureInterface $figure): void;

    public function getFigure(): FigureInterface;

    public function isTurnForPlayer(bool $currentTurn): bool;
}
