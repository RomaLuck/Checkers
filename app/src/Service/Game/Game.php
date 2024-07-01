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
        private CheckerDesk     $desk,
        private White           $white,
        private Black           $black,
        private LoggerInterface $logger
    )
    {
    }

    public function getDesk(): CheckerDesk
    {
        return $this->desk;
    }

    private function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return array<array>
     */
    public function makeMove(string $from, string $to): array
    {
        try {
            $cellFrom = $this->transformInputData($from);
            $cellTo = $this->transformInputData($to);
        } catch (RuntimeException $e) {
            $this->getLogger()->warning($e->getMessage());
            return $this->getDesk()->getDeskData();
        }

        $playerDetector = new PlayerDetector($this->white, $this->black);
        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);
        $player = $playerDetector->detect($selectedTeamNumber);
        if (!$player) {
            $this->getLogger()->warning('Can not find player on this cell');
            return $this->getDesk()->getDeskData();
        }

        $figure = (new FigureFactory($selectedTeamNumber))->create();

        $player->setFigure($figure);

        $rules = new Rules($player, $this->getDesk()->getDeskData(), $this->logger);

        if (!$this->isValidMove($cellFrom, $cellTo, $rules)) {
            return $this->getDesk()->getDeskData();
        }

        $this->getDesk()->updateDesk($cellFrom, $cellTo, $selectedTeamNumber);
        $this->getDesk()->updateFigures();
        $this->getLogger()->info("{$player->getName()} : [{$from}] => [{$to}]");

        if ($this->isGameOver()) {
            $this->getLogger()->info('GAME OVER');
        }

        return $this->getDesk()->getDeskData();
    }

    public function isValidRawMove(array $cellFrom, array $cellTo): bool
    {
        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);
        $playerDetector = new PlayerDetector($this->white, $this->black);
        $player = $playerDetector->detect($selectedTeamNumber);
        if (!$player) {
            return false;
        }

        $figure = (new FigureFactory($selectedTeamNumber))->create();

        $player->setFigure($figure);

        $rules = new Rules($player, $this->getDesk()->getDeskData(), $this->logger);

        $figuresForBeat = $this->findFiguresForBeat($cellFrom, $cellTo);
        if (count($figuresForBeat) > 0 && $rules->checkForBeat($cellFrom, $cellTo)) {
            return true;
        }

        if ($rules->checkForMove($cellFrom, $cellTo)) {
            return true;
        }

        return false;
    }

    private function isValidMove(array $cellFrom, array $cellTo, Rules $rules): bool
    {
        $figuresForBeat = $this->findFiguresForBeat($cellFrom, $cellTo);
        if (count($figuresForBeat) > 0 && $rules->checkForBeat($cellFrom, $cellTo)) {
            $this->getDesk()->clearCells($figuresForBeat);
            return true;
        }

        if ($rules->checkForMove($cellFrom, $cellTo)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<int> $from
     * @param array<int> $to
     */
    public function findFiguresForBeat(array $from, array $to): array
    {
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
     * @return array<int,int>
     */
    private function transformInputData(string $cell): array
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
}
