<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\Rule\RuleInterface;

interface FigureInterface
{
    /**
     * @return array<int>
     */
    public function getId(): array;

    /**
     * @return RuleInterface[]
     */
    public function getFigureRules(): array;
}
