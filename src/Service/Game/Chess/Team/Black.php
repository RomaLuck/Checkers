<?php

namespace App\Service\Game\Chess\Team;

class Black extends Team
{
    public function getTeamNumbers(): array
    {
        return [7, 8, 9, 10, 11, 12];
    }

}