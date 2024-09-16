<?php

namespace App\Service\Game\Chess\Team;

class White extends Team
{
    public function getTeamNumbers(): array
    {
        return [1, 2, 3, 4, 5, 6];
    }
}