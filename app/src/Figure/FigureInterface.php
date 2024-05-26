<?php

namespace Src\Figure;

interface FigureInterface
{
    public function getAvailableCommandNumbers(): array;

    public function getAvailableDirections(): array;

    public function getStepOpportunityForMove(): int;

    public function getStepOpportunityForAttack(): int;
}