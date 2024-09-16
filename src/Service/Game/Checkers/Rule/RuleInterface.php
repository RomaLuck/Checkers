<?php

declare(strict_types=1);

namespace App\Service\Game\Checkers\Rule;

use App\Service\Game\Checkers\Team\PlayerInterface;
use App\Service\Game\Move;

interface RuleInterface
{
    public function check(PlayerInterface $player, Move $move): bool;

    public function getMessage(): string;
}
