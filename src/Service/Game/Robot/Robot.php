<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Service\Game\Game;
use App\Service\Game\Team\PlayerInterface;
use Psr\Log\LoggerInterface;

final class Robot
{
    public function __construct(
        private Game            $game,
        private PlayerInterface $computer,
        private PlayerInterface $opponent,
        private int             $maxDepth
    )
    {
    }

    /**
     * @return array<array>
     */
    public function run(array $originalBoard, LoggerInterface $logger): array
    {
        $clonedBoard = $originalBoard;
        $bestMove = $this->bestMove($this->computer, $this->opponent, $clonedBoard)[1];
        if (is_array($bestMove)) {
            return $this->game->makeMove($originalBoard, $bestMove[0], $bestMove[1], $logger);
        }

        return $originalBoard;
    }

    /**
     *  Calculates the best move using the minimax algorithm.
     *
     * @param array<array> $board The current state of the game board.
     * @param int $depth The current depth of recursion.
     * @param bool $isMaximizingPlayer Indicates if the current move is for the maximizing player.
     * @return array An array containing the best score and the best move.
     * @return array<array>
     */
    public function bestMove(
        PlayerInterface $computerTeam,
        PlayerInterface $oppositeTeam,
        array           $board,
        int             $depth = 0,
        bool            $isMaximizingPlayer = true
    ): array
    {
        if ($depth === $this->maxDepth || $this->isGameOver($board)) {
            $evaluate = $this->evaluate($board, $computerTeam->getTeamNumbers());
            return [$evaluate, null];
        }

        $bestMove = null;

        if ($isMaximizingPlayer) {
            $bestScore = -PHP_INT_MAX;
            foreach ($this->getPossibleMoves($board, $computerTeam) as $move) {
                $board = $this->makeMove($board, $move);
                $score = $this->bestMove($computerTeam, $oppositeTeam, $board, $depth + 1, false)[0];
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMove = $move;
                }
            }
        } else {
            $bestScore = PHP_INT_MAX;
            foreach ($this->getPossibleMoves($board, $oppositeTeam) as $move) {
                $board = $this->makeMove($board, $move);
                $score = $this->bestMove($computerTeam, $oppositeTeam, $board, $depth + 1, true)[0];
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
     * @return array<array>
     */
    public function getPossibleMoves(array $board, PlayerInterface $player): array
    {
        $possibleMoves = [];

        foreach ($board as $rowKey => $row) {
            foreach ($row as $key => $cell) {
                $from = [$rowKey, $key];
                if (!in_array($cell, $player->getTeamNumbers())) {
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
     * @param array<array> $board
     * @param array<int,int> $computerTeamNumbers
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
     * @param array<array> $move
     * @return array<array>
     */
    public function makeMove(array $board, array $move): array
    {
        [$cellFrom, $cellTo] = $move;

        return $this->game->makeMove($board, $cellFrom, $cellTo);
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