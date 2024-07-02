<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Service\Game\Game;
use App\Service\Game\Team\PlayerInterface;

class Robot
{
    private int $maxDepth;
    private Game $game;

    public function __construct(
        private Game            $originalGame,
        private PlayerInterface $computerTeam,
        int                     $maxDepth)
    {
        $this->maxDepth = $maxDepth;
        $this->game = clone $this->originalGame;
        $this->game->setLogger(null);
    }

    /**
     * @return array<array>
     */
    public function run(): array
    {
        $bestMove = $this->bestMove()[1];
        if (is_array($bestMove)) {
            return $this->originalGame->makeMove($bestMove[0], $bestMove[1]);
        }

        return $this->originalGame->getDesk()->getDeskData();
    }

    /**
     * @return array<array>
     */
    public function bestMove($depth = 0, $isMaximizingPlayer = true): array
    {
        $board = $this->game->getDesk()->getDeskData();

        if ($depth === $this->maxDepth || $this->isGameOver()) {
            return [$this->evaluate($board, $this->computerTeam->getTeamNumbers()), null];
        }

        $bestMove = null;

        if ($isMaximizingPlayer) {
            $bestScore = -PHP_INT_MAX;
            foreach ($this->getPossibleMoves($board) as $move) {
                $this->makeMove($move);
                $score = $this->bestMove($depth + 1, false)[0];
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMove = $move;
                }
            }
        } else {
            $bestScore = PHP_INT_MAX;
            foreach ($this->getPossibleMoves($board) as $move) {
                $this->makeMove($move);
                $score = $this->bestMove($depth + 1, true)[0];
                if ($score < $bestScore) {
                    $bestScore = $score;
                    $bestMove = $move;
                }
            }
        }

        return [$bestScore, $bestMove];
    }

    /**
     * @param array<array> $board
     * @param array<int> $computerTeamNumbers
     */
    private function evaluate(array $board, array $computerTeamNumbers): int
    {
        $computerCount = 0;
        $opponentCount = 0;

        foreach ($board as $row) {
            foreach ($row as $cell) {
                if (in_array($cell, $computerTeamNumbers)) {
                    $computerCount++;
                } elseif ($cell !== 0) {
                    $opponentCount++;
                }
            }
        }

        return $computerCount - $opponentCount;
    }

    /**
     * @param array<array> $board
     * @return array<array>
     */
    public function getPossibleMoves(array $board): array
    {
        $possibleMoves = [];

        foreach ($board as $rowKey => $row) {
            foreach ($row as $key => $cell) {
                $from = [$rowKey, $key];
                if (!in_array($board[$from[0]][$from[1]], $this->computerTeam->getTeamNumbers())) {
                    continue;
                }

                $possibleDestinations = $this->getPossibleDestinations($from);

                $destinations = [];
                foreach ($possibleDestinations as $possibleDestination) {
                    if (!isset($board[$possibleDestination[0]][$possibleDestination[1]])) {
                        continue;
                    }

                    if ($this->game->isValidPlayerMove($from, $possibleDestination)) {
                        $possibleMoves[] = [$from, $possibleDestination];
                        continue;
                    }

                    if ($board[$possibleDestination[0]][$possibleDestination[1]] > 0
                        && !in_array($board[$possibleDestination[0]][$possibleDestination[1]], $this->computerTeam->getTeamNumbers())
                    ) {
                        $stepsX = $possibleDestination[0] - $from[0];
                        $stepsY = $possibleDestination[1] - $from[1];
                        $destinations[] = [$from[0] + $stepsX * 2, $from[0] + $stepsY * 2];
                    }
                }

                foreach ($destinations as $destination) {
                    if ($this->game->isValidPlayerMove($from, $destination)) {
                        $possibleMoves[] = [$from, $destination];
                    }
                }
            }
        }

        return $possibleMoves;
    }

    /**
     * @param array<array> $move
     */
    private function makeMove(array $move): void
    {
        [$cellFrom, $cellTo] = $move;
        $this->game->makeMove($cellFrom, $cellTo);
    }

    private function isGameOver(): bool
    {
        return $this->game->isGameOver();
    }

    /**
     * @param array<int> $from
     * @return array<array>
     */
    private function getPossibleDestinations(array $from): array
    {
        $direction = $this->computerTeam->getDirection();
        $step = 1;

        return [
            [$from[0] + $step, $from[1] + $direction],
            [$from[0] - $step, $from[1] + $direction]
        ];
    }
}