<?php

declare(strict_types=1);

namespace App\Service\Game;

final class InputTransformer
{
    /**
     * @return array<int,int>
     */
    public function transformInputData(string $cell): array
    {
        if (!preg_match('!^(?<letter>[[:alpha:]]+)(?<number>\d+)$!iu', $cell, $splitCell)) {
            throw new \RuntimeException('Cell is incorrect');
        }

        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        $key = array_search($splitCell['letter'], $letters, true);

        if ($key === false || $splitCell['number'] <= 0 || $splitCell['number'] > 8) {
            throw new \RuntimeException('Cell is unavailable');
        }

        return [$key, $splitCell['number'] - 1];
    }

    /**
     * @return array<array>
     */
    public function transformInputToArray(string $from, string $to): array
    {
        $cellFrom = $this->transformInputData($from);
        $cellTo = $this->transformInputData($to);

        return [$cellFrom, $cellTo];
    }

    /**
     * @param array<int> $cell
     */
    public function transformCellToString(array $cell): string
    {
        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

        return $letters[$cell[0]] . $cell[1] + 1;
    }
}
