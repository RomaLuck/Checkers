<?php

declare(strict_types=1);

namespace App\Service\Game\Team;

final class PlayerDetector
{
    public function __construct(
        private PlayerInterface $white,
        private PlayerInterface $black
    ) {
    }

    public function detect(int $teamNumber): ?PlayerInterface
    {
        if ($teamNumber > 0) {
            if (White::isWhiteNumber($teamNumber)) {
                return $this->white;
            }
            if (Black::isBlackNumber($teamNumber)) {
                return $this->black;
            }
        }
        return null;
    }
}
