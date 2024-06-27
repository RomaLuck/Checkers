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
    private Rules $rules;
    private PlayerDetector $playerDetector;

    public function __construct(
        private CheckerDesk     $desk,
        private White           $white,
        private Black           $black,
        private LoggerInterface $logger
    )
    {
        $this->playerDetector = new PlayerDetector($white, $black);
        $this->rules = new Rules($this->getLogger());
    }

    public function getDesk(): CheckerDesk
    {
        return $this->desk;
    }

    private function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    private function getRules(): Rules
    {
        return $this->rules;
    }

    private function getPlayerDetector(): PlayerDetector
    {
        return $this->playerDetector;
    }

    /**
     * @return array<array>
     */
    public function proceed(string $from, string $to): array
    {
        try {
            $cellFrom = $this->transformInputData($from);
            $cellTo = $this->transformInputData($to);
        } catch (RuntimeException $e) {
            $this->getLogger()->warning($e->getMessage());
            return $this->getDesk()->getDeskData();
        }

        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);
        $player = $this->getPlayerDetector()->detect($selectedTeamNumber);
        $figure = (new FigureFactory($selectedTeamNumber))->create();

        $player->setFigure($figure);
        $this->getRules()->setPlayer($player);
        $this->getRules()->setDesk($this->getDesk()->getDeskData());

        if (!$this->isValidMove($cellFrom, $cellTo)) {
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

    private function isValidMove(array $cellFrom, array $cellTo): bool
    {
        $figuresForBeat = $this->getRules()->findFiguresForBeat($cellFrom, $cellTo);
        if (count($figuresForBeat) > 0 && $this->getRules()->checkForBeat($cellFrom, $cellTo)) {
            $this->getDesk()->clearCells($figuresForBeat);
            return true;
        }

        if ($this->getRules()->checkForMove($cellFrom, $cellTo)) {
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
