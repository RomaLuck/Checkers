<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Move;
use App\Service\Game\Team\PlayerInterface;

final class IsCorrectStep implements RuleInterface
{
    public function check(PlayerInterface $player, Move $move): bool
    {
        return abs($move->getTo()[0] - $move->getFrom()[0]) === abs($move->getTo()[1] - $move->getFrom()[1]);
    }

    public function getMessage(): string
    {
        return 'The step is not correct';
    }
}
