<?php

namespace App\Service\Game\Chess\Team;

class TeamDetector
{

    public function __construct(
        private TeamInterface $white,
        private TeamInterface $black
    ) {
    }

    public function detect(int $teamNumber): ?TeamInterface
    {
        if ($teamNumber > 0) {
            if ($this->white->getTeamNumbers()) {
                return $this->white;
            }
            if ($this->black->getTeamNumbers()) {
                return $this->black;
            }
        }

        return null;
    }

}