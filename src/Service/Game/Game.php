<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Figure\FigureFactory;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\PlayerDetector;
use App\Service\Game\Team\PlayerInterface;
use App\Service\Game\Team\White;
use Psr\Log\LoggerInterface;

final class Game
{
    private InputTransformer $inputTransformer;
    private CheckerDeskService $checkerDeskService;

    public function __construct(
        private White $white,
        private Black $black,
    )
    {
        $this->inputTransformer = new InputTransformer();
        $this->checkerDeskService = new CheckerDeskService();
    }

    public function getWhite(): White
    {
        return $this->white;
    }

    public function getBlack(): Black
    {
        return $this->black;
    }

    /**
     * @return array<array>
     */
    public function makeMove(array $desk, array $cellFrom, array $cellTo, ?LoggerInterface $logger = null): array
    {
        $player = $this->detectPlayer($desk, $cellFrom);
        if (!$player) {
            $logger?->warning('Can not find player on this cell');
            return $desk;
        }

        if (!$this->isValidMove($player, $desk, $cellFrom, $cellTo, $logger)) {
            return $desk;
        }

        $figuresForBeat = $this->checkerDeskService->findFiguresForBeat($player, $desk, $cellFrom, $cellTo);
        if ($figuresForBeat !== []) {
            $desk = $this->checkerDeskService->clearCells($desk, $figuresForBeat);

            $transFormedFiguresForBeat = array_map(
                fn($figure) => $this->inputTransformer->transformCellToString($figure),
                $figuresForBeat
            );
            $logger?->warning('--removed: ['
                . implode(',', $transFormedFiguresForBeat) .
                '] checkers');
        }

        $from = $this->inputTransformer->transformCellToString($cellFrom);
        $to = $this->inputTransformer->transformCellToString($cellTo);
        $logger?->info("{$player->getName()} : [{$from}] => [{$to}]");

        if ($this->isGameOver($desk)) {
            $logger?->info('Game over');
            $winner = $this->getAdvantagePlayer($desk)->getName();
            $logger?->info("$winner won!");
        }

        return $this->checkerDeskService->updateData($desk, $cellFrom, $cellTo);
    }

    public function makeMoveWithCellTransform(
        array            $desk,
        string           $cellFrom,
        string           $cellTo,
        ?LoggerInterface $logger = null
    ): array
    {
        [$cellFromTransformed, $cellToTransformed] = $this->inputTransformer->transformInputToArray($cellFrom, $cellTo);
        if (!is_array($cellFromTransformed) || !is_array($cellToTransformed)) {
            $logger?->warning('Cells are not transformed');
            return $desk;
        }

        if ($cellFromTransformed === [] || $cellToTransformed === []) {
            return $desk;
        }

        return $this->makeMove($desk, $cellFromTransformed, $cellToTransformed, $logger);
    }

    /**
     * @param array<array> $desk
     */
    public function isGameOver(array $desk): bool
    {
        return $this->countFigures($desk, White::WHITE_NUMBERS) === 0
            || $this->countFigures($desk, Black::BLACK_NUMBERS) === 0;
    }

    /**
     * @param array<array> $desk
     */
    public function getAdvantagePlayer(array $desk): PlayerInterface
    {
        return $this->countFigures($desk, White::WHITE_NUMBERS)
        > $this->countFigures($desk, Black::BLACK_NUMBERS)
            ? $this->white
            : $this->black;
    }

    /**
     * @param array<array> $desk
     * @param array<int,int> $cellFrom
     */
    private function detectPlayer(array $desk, array $cellFrom): ?PlayerInterface
    {
        $playerDetector = new PlayerDetector($this->white, $this->black);
        $selectedTeamNumber = $this->checkerDeskService->getSelectedTeamNumber($desk, $cellFrom);

        return $playerDetector->detect($selectedTeamNumber);
    }

    /**
     * @param array<int,int> $cellFrom
     * @param array<array> $desk
     */
    private function setPlayerFigure(PlayerInterface $player, array $desk, array $cellFrom): void
    {
        $selectedTeamNumber = $this->checkerDeskService->getSelectedTeamNumber($desk, $cellFrom);
        $figure = (new FigureFactory($selectedTeamNumber))->create();
        $player->setFigure($figure);
    }

    /**
     * @param array<int,int> $cellFrom
     * @param array<int,int> $cellTo
     */
    public function isValidMove(
        PlayerInterface  $player,
        array            $desk,
        array            $cellFrom,
        array            $cellTo,
        ?LoggerInterface $logger = null
    ): bool
    {
        $this->setPlayerFigure($player, $desk, $cellFrom);

        $rules = new Rules($player, $desk, $logger);

        $figuresForBeat = $this->checkerDeskService->findFiguresForBeat($player, $desk, $cellFrom, $cellTo);
        if (count($figuresForBeat) > 0) {
            if ($rules->checkForBeat($cellFrom, $cellTo)) {
                return true;
            }
            return false;
        }

        if ($rules->checkForMove($cellFrom, $cellTo)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<array> $desk
     * @param array<int,int> $figureNumbers
     */
    private function countFigures(array $desk, array $figureNumbers): int
    {
        $count = 0;

        foreach ($desk as $row) {
            foreach ($row as $cell) {
                if (in_array($cell, $figureNumbers, true)) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
