<?php

declare(strict_types=1);

namespace Src;

final class CheckerDesk
{
    public static function initDesk(): array
    {
        return [
            [1, -1, 1, -1, 0, -1, 2, -1],
            [-1, 1, -1, 0, -1, 2, -1, 2],
            [1, -1, 1, -1, 0, -1, 2, -1],
            [-1, 1, -1, 0, -1, 2, -1, 2],
            [1, -1, 1, -1, 0, -1, 2, -1],
            [-1, 1, -1, 0, -1, 2, -1, 2],
            [1, -1, 1, -1, 0, -1, 2, -1],
            [-1, 1, -1, 0, -1, 2, -1, 2],
        ];
    }
}