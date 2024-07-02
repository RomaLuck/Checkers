<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Team\PlayerInterface;

class IsCorrectStep implements RuleInterface
{

    public function check(PlayerInterface $player, array $from, array $to): bool
    {
        return abs($to[0] - $from[0]) === abs($to[1] - $from[1]);
    }

    public function getMessage(): string
    {
        return 'The step is not correct';
    }
}