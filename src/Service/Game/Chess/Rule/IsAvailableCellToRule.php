<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Rule;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;

final class IsAvailableCellToRule implements RuleInterface
{
    public function check(TeamInterface $team, Move $move, BoardAbstract $board): bool
    {
        $board = $board->getBoardData();
        $to = $move->getTo();
        $toNumber = $board[$to[0]][$to[1]] ?? '';

        return $toNumber !== '' && $toNumber >= 0 && !in_array($toNumber, $team->getTeamNumbers());
    }

    public function getMessage(): string
    {
        return 'Cell to is not available';
    }
}
