<?php

declare(strict_types=1);

namespace App\Service\Game\Checkers\Rule;

use App\Service\Game\Checkers\Team\PlayerInterface;
use App\Service\Game\Move;

final class IsAvailableCellRule implements RuleInterface
{
    public function __construct(private array $desk)
    {
    }

    public function check(PlayerInterface $player, Move $move): bool
    {
        $to = $move->getTo();

        return isset($this->desk[$to[0]][$to[1]]) && $this->desk[$to[0]][$to[1]] === 0;
    }

    public function getMessage(): string
    {
        return 'Cell is not available';
    }
}
