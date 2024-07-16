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

    public function __construct(
        private CheckerDesk      $desk,
        private White            $white,
        private Black            $black,
        private ?LoggerInterface $logger,
    ) {
        $this->inputTransformer = new InputTransformer();
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

    /**
     * @return array<array>
     */
    public function makeMove(mixed $cellFrom, mixed $cellTo, bool $transformInput = false): array
    {
        if (is_string($cellFrom) && is_string($cellTo) && $transformInput) {
            [$cellFrom, $cellTo] = $this->inputTransformer->transformInputToArray($cellFrom, $cellTo);
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
        if (!$this->isValidMove($player, $cellFrom, $cellTo, $rules)) {
            return $this->getDesk()->getDeskData();
        }

        $this->updateData($cellFrom, $cellTo, $player);

        return $this->getDesk()->getDeskData();
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
        if ($this->isValidMove($player, $cellFrom, $cellTo, $rules, false)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<int,int> $from
     * @param array<int,int> $to
     */
    public function findFiguresForBeat(PlayerInterface $player, array $from, array $to): array
    {
        $desk = $this->getDesk()->getDeskData();
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

    public function isGameOver(): bool
    {
        return $this->countFigures(White::WHITE_NUMBERS) === 0
            || $this->countFigures(Black::BLACK_NUMBERS) === 0;
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

        $from = $this->inputTransformer->transformCellToString($cellFrom);
        $to = $this->inputTransformer->transformCellToString($cellTo);
        $this->getLogger()?->info("{$player->getName()} : [{$from}] => [{$to}]");
    }

    private function getLogger(): ?LoggerInterface
    {
        return $this->logger;
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
    private function isValidMove(
        PlayerInterface $player,
        array $cellFrom,
        array $cellTo,
        Rules $rules,
        bool $clearCells = true
    ): bool {
        $figuresForBeat = $this->findFiguresForBeat($player, $cellFrom, $cellTo);
        if (count($figuresForBeat) > 0 && $rules->checkForBeat($cellFrom, $cellTo)) {
            if ($clearCells) {
                $this->getDesk()->clearCells($figuresForBeat);

                $transFormedFiguresForBeat = array_map(
                    fn ($figure) => $this->inputTransformer->transformCellToString($figure),
                    $figuresForBeat
                );
                $this->logger?->warning('--removed: ['
                    . implode(',', $transFormedFiguresForBeat) .
                    '] checkers');
            }
            return true;
        }

        if ($rules->checkForMove($cellFrom, $cellTo)) {
            return true;
        }

        return false;
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
}
