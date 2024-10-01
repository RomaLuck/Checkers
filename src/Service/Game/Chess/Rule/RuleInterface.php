<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Rule;

use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;

interface RuleInterface
{
    public function check(TeamInterface $team, Move $move): bool;

    public function getMessage(): string;
}
