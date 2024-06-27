<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Team\PlayerInterface;
use Psr\Log\LoggerInterface;

class Robot
{
    private Rules $rules;

    public function __construct(
        private CheckerDesk     $desk,
        private PlayerInterface $computerTeam,
        private LoggerInterface $logger
    )
    {
        $this->rules = new Rules($this->getLogger());
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getDesk(): CheckerDesk
    {
        return $this->desk;
    }

    private function getRules(): Rules
    {
        return $this->rules;
    }

    public function run(): array
    {
        $move = $this->generateMove();
        if ($move === []) {
            return $this->getDesk()->getDeskData();
        }

        [$cellFrom, $cellTo] = $move;
        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);
        $this->getDesk()->updateDesk($cellFrom, $cellTo, $selectedTeamNumber);
        $this->getDesk()->updateFigures();

        $from = $this->transformCellToString($cellFrom);
        $to = $this->transformCellToString($cellTo);
        $this->getLogger()->info("{$this->computerTeam->getName()} : [{$from}] => [{$to}]");
        return $this->getDesk()->getDeskData();
    }

    private function transformCellToString(array $cell): string
    {
        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        return $letters[$cell[0]] . $cell[1] + 1;
    }

    private function getTransformedCells(): array
    {
        $transformedTable = [];
        foreach ($this->desk->getDeskData() as $deskRowKey => $deskRow) {
            foreach ($deskRow as $key => $number) {
                $transformedTable[] = [$deskRowKey, $key];
            }
        }

        return $transformedTable;
    }

    public function generateMove(): array
    {
        $deskData = $this->desk->getDeskData();
        $transformedCells = $this->getTransformedCells();
        $cellsHashMap = [];

        foreach ($transformedCells as $cell) {
            if (in_array($deskData[$cell[0]][$cell[1]], $this->computerTeam->getTeamNumbers())) {
                $computerCell = $cell;
                $goalCells = array_filter($transformedCells, function ($cell) use ($computerCell, $deskData) {
                    $firstNumberDifference = abs($cell[0] - $computerCell[0]);
                    $secondNumberDifference = abs($cell[1] - $computerCell[1]);

                    return $deskData[$cell[0]][$cell[1]] > 0
                        && $deskData[$cell[0]][$cell[1]] !== $deskData[$computerCell[0]][$computerCell[1]]
                        && $firstNumberDifference === $secondNumberDifference;
                });

                foreach ($goalCells as $goalCell) {
                    $path = $this->getPath($computerCell, $goalCell, $deskData);
                    if ($path !== []) {
                        $cellsHashMap[] = [
                            'cell' => $computerCell,
                            'goalCell' => $goalCell,
                            'path' => $path
                        ];
                    }
                }
            }
        }

        $minPathArray = array_reduce($cellsHashMap, function ($carry, $item) {
            if ($carry === null || count($item['path']) < count($carry['path'])) {
                $carry = $item;
            }
            return $carry;
        });

        if (is_array($minPathArray) && is_array($minPathArray['path'])) {
            return [$minPathArray['cell'], $minPathArray['path'][0]];
        }

        return [];
    }

    /**
     * @param array<int,int> $computerCell
     * @param array<int,int> $goalCell
     * @param array<array> $deskData
     * @return array<array>
     */
    public function getPath(array $computerCell, array $goalCell, array $deskData): array
    {
        $path = [];
        $stepsX = $goalCell[0] - $computerCell[0];
        $stepsY = $goalCell[1] - $computerCell[1];
        $dirX = $stepsX / abs($stepsX);
        $dirY = $stepsY / abs($stepsY);

        for ($i = 1; $i < abs($stepsX); $i++) {
            $probablyStep = [$computerCell[0] + $i * $dirX, $computerCell[1] + $i * $dirY];
            if ($deskData[$probablyStep[0]][$probablyStep[1]] !== 0) {
                break;
            }
            $path[] = $probablyStep;
        }

        return $path;
    }
}