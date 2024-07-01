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

        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);
        $playerDetector = new PlayerDetector($this->white, $this->black);
        $player = $playerDetector->detect($selectedTeamNumber);
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

    private function isValidMove(array $cellFrom, array $cellTo, Rules $rules): bool
    {
        $figuresForBeat = $rules->findFiguresForBeat($cellFrom, $cellTo);
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

    private function isGameOver(): bool
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
