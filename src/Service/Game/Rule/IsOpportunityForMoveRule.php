<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Move;
use App\Service\Game\Team\PlayerInterface;

final class IsOpportunityForMoveRule implements RuleInterface
{
    public function check(PlayerInterface $player, Move $move): bool
    {
        $step = abs($move->getTo()[1] - $move->getFrom()[1]);

        return $step <= $player->getFigure()->getStepOpportunityForMove();
    }

    public function getMessage(): string
    {
        return 'You do not have ability to reach this cell';
    }
}
