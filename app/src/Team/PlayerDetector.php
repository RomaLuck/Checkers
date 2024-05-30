<?php

namespace Src\Team;

use RuntimeException;

class PlayerDetector
{
    public function __construct(
        private PlayerInterface $white,
        private PlayerInterface $black
    ){
    }

    public function detect(int $teamNumber): PlayerInterface
    {
        if ($teamNumber > 0) {
            if (in_array($teamNumber, White::WHITE_NUMBERS, true)) {
                return $this->white;
            }
            if (in_array($teamNumber, Black::BLACK_NUMBERS, true)) {
                return $this->black;
            }
        }
        throw new RuntimeException('Can not find player on this cell');
    }
}