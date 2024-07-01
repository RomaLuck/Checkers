<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Team\PlayerInterface;

class IsOpportunityForBeatRule implements RuleInterface
{

    public function check(PlayerInterface $player, array $from, array $to): bool
    {
        $step = abs($to[1] - $from[1]);

        return $step <= $player->getFigure()->getStepOpportunityForAttack();
    }

    public function getMessage(): string
    {
        return 'You do not have ability to reach this cell';
    }
}