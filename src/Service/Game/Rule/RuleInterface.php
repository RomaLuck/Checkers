<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Move;
use App\Service\Game\Team\PlayerInterface;

interface RuleInterface
{
    public function check(PlayerInterface $player, Move $move): bool;

    public function getMessage(): string;
}
