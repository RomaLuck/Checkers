<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Rule;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;

class IsOpportunityForMoveRule implements RuleInterface
{
    private int $step;

    public function __construct(int $step)
    {
        $this->step = $step;
    }

    public function check(TeamInterface $team, Move $move, BoardAbstract $board): bool
    {
        $step = abs($move->getTo()[1] - $move->getFrom()[1]);
        if ($step === 0) {
            $step = abs($move->getTo()[0] - $move->getFrom()[0]);
        }

        return $step <= $this->step;
    }

    public function getMessage(): string
    {
        return 'You do not have ability to reach this cell';
    }
}
