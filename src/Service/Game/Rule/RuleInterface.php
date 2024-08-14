<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Team\PlayerInterface;

interface RuleInterface
{
    /**
     * @param array<int> $from
     * @param array<int> $to
     */
    public function check(PlayerInterface $player, array $from, array $to): bool;

    public function getMessage(): string;
}
