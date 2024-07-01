<?php

declare(strict_types=1);

namespace App\Service\Game\Team;

use RuntimeException;

final class PlayerDetector
{
    public function __construct(
        private PlayerInterface $white,
        private PlayerInterface $black
    )
    {
    }

    public function detect(int $teamNumber): ?PlayerInterface
    {
        if ($teamNumber > 0) {
            if (in_array($teamNumber, White::WHITE_NUMBERS, true)) {
                return $this->white;
            }
            if (in_array($teamNumber, Black::BLACK_NUMBERS, true)) {
                return $this->black;
            }
        }
        return null;
    }
}
