<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Rule;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;

final class IsAvailableCellFromRule implements RuleInterface
{
    public function __construct(private BoardAbstract $board)
    {
    }

    public function check(TeamInterface $team, Move $move): bool
    {
        $board = $this->board->getBoardData();
        $from = $move->getFrom();
        $fromNumber = $board[$from[0]][$from[1]] ?? '';

        return $fromNumber !== '' && in_array($fromNumber, $team->getTeamNumbers());
    }

    public function getMessage(): string
    {
        return 'Cell from is not available';
    }
}
