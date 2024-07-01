<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Team\PlayerInterface;

class IsAvailableCellRule implements RuleInterface
{

    public function __construct(private array $desk)
    {
    }

    public function check(PlayerInterface $player, array $from, array $to): bool
    {
        return array_key_exists($to[0], $this->desk)
            && array_key_exists($to[1], $this->desk[$to[0]])
            && $this->desk[$to[0]][$to[1]] === 0;
    }

    public function getMessage(): string
    {
        return 'Cell is not available';
    }
}