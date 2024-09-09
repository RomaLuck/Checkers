<?php

declare(strict_types=1);

namespace App\Service\Game\Checkers\Figure;

interface FigureInterface
{
    public function getAvailableCommandNumbers(): array;

    public function getAvailableDirections(): array;

    public function getStepOpportunityForMove(): int;

    public function getStepOpportunityForAttack(): int;
}
