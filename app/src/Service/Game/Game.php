<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Figure\FigureFactory;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\PlayerDetector;
use App\Service\Game\Team\PlayerInterface;
use App\Service\Game\Team\White;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class Game
{

    public function __construct(
        private CheckerDesk      $desk,
        private White            $white,
        private Black            $black,
        private ?LoggerInterface $logger,
    )
    {
    }

    public function __clone(): void
    {
        $this->desk = clone $this->desk;
        $this->white = clone $this->white;
        $this->black = clone $this->black;
        $this->logger = clone $this->logger;
    }

    public function getDesk(): CheckerDesk
    {
        return $this->desk;
    }

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    private function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return array<array>
     */
    public function makeMove(mixed $cellFrom, mixed $cellTo, bool $transformInput = false): array
    {
        if (is_string($cellFrom) && is_string($cellTo) && $transformInput) {
            [$cellFrom, $cellTo] = $this->transformInputToArray($cellFrom, $cellTo);
            if ($cellFrom === [] || $cellTo === []) {
                return $this->getDesk()->getDeskData();
            }
        }

        if (!is_array($cellFrom) || !is_array($cellTo)) {
            $this->getLogger()?->warning('Cells are not transformed');
            return $this->getDesk()->getDeskData();
        }

        $player = $this->detectPlayer($cellFrom);
        if (!$player) {
            $this->getLogger()?->warning('Can not find player on this cell');
            return $this->getDesk()->getDeskData();
        }

        $this->setPlayerFigure($player, $cellFrom);

        $rules = new Rules($player, $this->getDesk()->getDeskData(), $this->logger);
        if (!$this->isValidMove($cellFrom, $cellTo, $rules)) {
            return $this->getDesk()->getDeskData();
        }

        $this->updateData($cellFrom, $cellTo, $player);

        return $this->getDesk()->getDeskData();
    }

    /**
     * @return array<array>
     */
    private function transformCellsToArray(string $from, string $to): array
    {
        try {
            $cellFrom = $this->transformInputData($from);
            $cellTo = $this->transformInputData($to);
        } catch (RuntimeException $e) {
            $this->getLogger()?->warning($e->getMessage());
            return [];
        }

        return [$cellFrom, $cellTo];
    }

    /**
     * @param array<int,int> $cellFrom
     */
    private function detectPlayer(array $cellFrom): ?PlayerInterface
    {
        $playerDetector = new PlayerDetector($this->white, $this->black);
        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);

        return $playerDetector->detect($selectedTeamNumber);
    }

    /**
     * @param array<int,int> $cellFrom
     */
    private function setPlayerFigure(PlayerInterface $player, array $cellFrom): void
    {
        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);
        $figure = (new FigureFactory($selectedTeamNumber))->create();
        $player->setFigure($figure);
    }

    /**
     * @param array<int,int> $cellFrom
     * @param array<int,int> $cellTo
     */
    public function isValidPlayerMove(array $cellFrom, array $cellTo): bool
    {
        $player = $this->detectPlayer($cellFrom);
        if (!$player) {
            $this->getLogger()?->warning('Can not find player on this cell');
            return false;
        }

        $this->setPlayerFigure($player, $cellFrom);

        $rules = new Rules($player, $this->getDesk()->getDeskData(), $this->logger);
        if ($this->isValidMove($cellFrom, $cellTo, $rules, false)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<int,int> $cellFrom
     * @param array<int,int> $cellTo
     */
    private function isValidMove(array $cellFrom, array $cellTo, Rules $rules, bool $clearCells = true): bool
    {
        $figuresForBeat = $this->findFiguresForBeat($cellFrom, $cellTo);
        if (count($figuresForBeat) > 0 && $rules->checkForBeat($cellFrom, $cellTo)) {
            if ($clearCells) {
                $this->getDesk()->clearCells($figuresForBeat);
            }
            return true;
        }

        if ($rules->checkForMove($cellFrom, $cellTo)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<int,int> $from
     * @param array<int,int> $to
     */
    public function findFiguresForBeat(array $from, array $to): array
    {
        #todo виправити баг з видаленням лишніх клітин
        $desk = $this->getDesk()->getDeskData();
        $figuresCells = [];
        $letters = [$from[0], $to[0]];
        $numbers = [$from[1], $to[1]];

        for ($i = min($letters) + 1; $i < max($letters); $i++) {
            for ($j = min($numbers) + 1; $j < max($numbers); $j++) {
                if (isset($desk[$i][$j]) && $desk[$i][$j] > 0) {
                    $figuresCells[] = [$i, $j];
                }
            }
        }

        return $figuresCells;
    }

    /**
     * @param array<int,int> $cell
     */
    private function transformCellToString(array $cell): string
    {
        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        return $letters[$cell[0]] . $cell[1] + 1;
    }

    /**
     * @return array<int,int>
     */
    public function transformInputData(string $cell): array
    {
        if (!preg_match('!^(?<letter>[[:alpha:]]+)(?<number>\d+)$!iu', $cell, $splitCell)) {
            throw new RuntimeException('Cell is incorrect');
        }

        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        $key = array_search($splitCell['letter'], $letters, true);

        if ($key === false || $splitCell['number'] <= 0 || $splitCell['number'] > 8) {
            throw new RuntimeException('Cell is unavailable');
        }

        return [$key, $splitCell['number'] - 1];
    }

    public function isGameOver(): bool
    {
        return $this->countFigures(White::WHITE_NUMBERS) === 0
            || $this->countFigures(Black::BLACK_NUMBERS) === 0;
    }

    /**
     * @param array<int> $figureNumbers
     */
    private function countFigures(array $figureNumbers): int
    {
        $count = 0;

        foreach ($this->getDesk()->getDeskData() as $row) {
            foreach ($row as $cell) {
                if (in_array($cell, $figureNumbers, true)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function getAdvantagePlayer(): PlayerInterface
    {
        return $this->countFigures(White::WHITE_NUMBERS)
        > $this->countFigures(Black::BLACK_NUMBERS)
            ? $this->white
            : $this->black;
    }

    /**
     * @param array<int,int> $cellFrom
     * @param array<int,int> $cellTo
     */
    public function updateData(array $cellFrom, array $cellTo, PlayerInterface $player): void
    {
        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);
        $this->getDesk()->updateDesk($cellFrom, $cellTo, $selectedTeamNumber);
        $this->getDesk()->updateFigures();

        $from = $this->transformCellToString($cellFrom);
        $to = $this->transformCellToString($cellTo);
        $this->getLogger()?->info("{$player->getName()} : [{$from}] => [{$to}]");
    }
}
