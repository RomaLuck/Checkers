<?php

namespace App\Service\Game\Chess\Rule;

use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;

class IsOpportunityForMoveRule implements RuleInterface
{

    private int $step;

    public function __construct(int $step)
    {
        $this->step = $step;
    }

    public function check(TeamInterface $team, Move $move): bool
    {
        $step = abs($move->getTo()[1] - $move->getFrom()[1]);

        return $step === $this->step;
    }

    public function getMessage(): string
    {
        return 'You do not have ability to reach this cell';
    }
}