<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\Rule\RuleInterface;

interface FigureInterface
{
    public function getId(): int;

    /**
     * @return RuleInterface[]
     */
    public function getFigureRules(): array;
}
