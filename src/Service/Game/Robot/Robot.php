<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Service\Game\Game;
use App\Service\Game\MoveResult;
use App\Service\Game\Team\PlayerInterface;
use Psr\Log\LoggerInterface;

final class Robot
{
    private int $maxDepth;

    public function __construct(
        private Game $game,
        private PlayerInterface $computer,
        private PlayerInterface $opponent,
    ) {
        $this->maxDepth = 3;
    }

    public function run(MoveResult $startCondition, LoggerInterface $logger): MoveResult
    {
        $moveResult = clone $startCondition;

        $bestMove = $this->bestMove($this->computer, $this->opponent, $moveResult)[1];
        if (is_array($bestMove)) {
            return $this->game->makeMove($startCondition, $bestMove[0], $bestMove[1], $logger);
        }

        return $startCondition;
    }

    /**
     *  Calculates the best move using the minimax algorithm.
     *
     * @return array An array containing the best score and the best move.
     * @return array<array>
     */
    public function bestMove(
        PlayerInterface $computerTeam,
        PlayerInterface $oppositeTeam,
        MoveResult $startCondition,
        int $depth = 0,
        bool $isMaximizingPlayer = true
    ): array {
        $board = $startCondition->getCheckerDesk();

        if ($depth === $this->maxDepth || $this->isGameOver($board)) {
            $evaluate = $this->evaluate($board, $computerTeam->getTeamNumbers());
            return [$evaluate, null];
        }

        $bestMove = null;

        if ($isMaximizingPlayer) {
            $bestScore = -PHP_INT_MAX;
            foreach ($this->getPossibleMoves($board, $computerTeam) as $move) {
                $moveResult = $this->makeMove($startCondition, $move);
                $score = $this->bestMove($computerTeam, $oppositeTeam, $moveResult, $depth + 1, false)[0];
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMove = $move;
                }
            }
        } else {
            $bestScore = PHP_INT_MAX;
            foreach ($this->getPossibleMoves($board, $oppositeTeam) as $move) {
                $moveResult = $this->makeMove($startCondition, $move);
                $score = $this->bestMove($computerTeam, $oppositeTeam, $moveResult, $depth + 1, true)[0];
                if ($score < $bestScore) {
                    $bestScore = $score;
                    $bestMove = $move;
                }
            }
        }

        return [$bestScore, $bestMove];
    }

    /**
     * @param array<array<int>> $board
     *
     * @return array<array<int>>
     */
    public function getPossibleMoves(array $board, PlayerInterface $player): array
    {
        $possibleMoves = [];

        foreach ($board as $rowKey => $row) {
            foreach ($row as $key => $cell) {
                $from = [$rowKey, $key];
                if (! in_array($cell, $player->getTeamNumbers())) {
                    continue;
                }

                $possibleDestinations = $this->getEmptyCells($board);
                foreach ($possibleDestinations as $destination) {
                    if ($this->game->isValidMove($player, $board, $from, $destination)) {
                        $possibleMoves[] = [$from, $destination];
                    }
                }
            }
        }

        return $possibleMoves;
    }

    /**
     * @param array<array<int>> $move
     */
    public function makeMove(MoveResult $startCondition, array $move): MoveResult
    {
        [$cellFrom, $cellTo] = $move;

        return $this->game->makeMove($startCondition, $cellFrom, $cellTo);
    }

    public function setMaxDepth(int $maxDepth): void
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * @param array<array<int>> $board
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

    private function isGameOver(array $board): bool
    {
        return $this->game->isGameOver($board);
    }

    private function getEmptyCells(array $board): array
    {
        $emptyCells = [];
        foreach ($board as $rowKey => $row) {
            foreach ($row as $key => $cell) {
                if ($cell === 0) {
                    $emptyCells[] = [$rowKey, $key];
                }
            }
        }

        return $emptyCells;
    }
}
