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
        private White            $white,
        private Black            $black,
        private ?LoggerInterface $logger,
    )
    {
        $this->inputTransformer = new InputTransformer();
        $this->checkerDeskService = new CheckerDeskService();
    }

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return array<array>
     */
    public function makeMove(array $desk, mixed $cellFrom, mixed $cellTo, bool $transformInput = false): array
    {
        #todo зарефакторити трансформацію вводу
        if (is_string($cellFrom) && is_string($cellTo) && $transformInput) {
            [$cellFrom, $cellTo] = $this->inputTransformer->transformInputToArray($cellFrom, $cellTo);
            if ($cellFrom === [] || $cellTo === []) {
                return $desk;
            }
        }

        if (!is_array($cellFrom) || !is_array($cellTo)) {
            $this->getLogger()?->warning('Cells are not transformed');
            return $desk;
        }

        $player = $this->detectPlayer($desk, $cellFrom);
        if (!$player) {
            $this->getLogger()?->warning('Can not find player on this cell');
            return $desk;
        }

        if (!$this->isValidMove($player, $desk, $cellFrom, $cellTo)) {
            return $desk;
        }

        $figuresForBeat = $this->findFiguresForBeat($player, $desk, $cellFrom, $cellTo);
        if ($figuresForBeat !== []) {
            $desk = $this->checkerDeskService->clearCells($desk, $figuresForBeat);

            $transFormedFiguresForBeat = array_map(
                fn($figure) => $this->inputTransformer->transformCellToString($figure),
                $figuresForBeat
            );
            $this->logger?->warning('--removed: ['
                . implode(',', $transFormedFiguresForBeat) .
                '] checkers');
        }

        return $this->updateData($desk, $cellFrom, $cellTo, $player);
    }

    /**
     * @param array<array> $desk
     * @param array<int,int> $from
     * @param array<int,int> $to
     */
    public function findFiguresForBeat(PlayerInterface $player, array $desk, array $from, array $to): array
    {
        $figuresCells = [];
        $directionX = $to[0] - $from[0] > 0 ? 1 : -1;
        $directionY = $to[1] - $from[1] > 0 ? 1 : -1;
        $currentX = $from[0] + $directionX;
        $currentY = $from[1] + $directionY;

        while ($currentX !== $to[0] && $currentY !== $to[1]) {
            if (isset($desk[$currentX][$currentY])
                && $desk[$currentX][$currentY] > 0
                && !in_array($desk[$currentX][$currentY], $player->getTeamNumbers())
            ) {
                $figuresCells[] = [$currentX, $currentY];
            }
            $currentX += $directionX;
            $currentY += $directionY;
        }

        return $figuresCells;
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
     * @param array<int,int> $cellFrom
     * @param array<int,int> $cellTo
     */
    public function updateData(array $desk, array $cellFrom, array $cellTo, PlayerInterface $player): array
    {
        $selectedTeamNumber = $this->checkerDeskService->getSelectedTeamNumber($desk, $cellFrom);
        $updatedDesk = $this->checkerDeskService->updateDesk($desk, $cellFrom, $cellTo, $selectedTeamNumber);
        $updatedDesk = $this->checkerDeskService->updateFigures($updatedDesk);

        $from = $this->inputTransformer->transformCellToString($cellFrom);
        $to = $this->inputTransformer->transformCellToString($cellTo);
        $this->getLogger()?->info("{$player->getName()} : [{$from}] => [{$to}]");

        return $updatedDesk;
    }

    private function getLogger(): ?LoggerInterface
    {
        return $this->logger;
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
        PlayerInterface $player,
        array           $desk,
        array           $cellFrom,
        array           $cellTo,
    ): bool
    {
        $this->setPlayerFigure($player, $desk, $cellFrom);

        $rules = new Rules($player, $desk, $this->logger);

        $figuresForBeat = $this->findFiguresForBeat($player, $desk, $cellFrom, $cellTo);
        if (count($figuresForBeat) > 0 && $rules->checkForBeat($cellFrom, $cellTo)) {
            return true;
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
