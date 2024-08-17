<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Service\Game\Game;
use App\Service\Game\Move;
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

        /** @var Move $bestMove */
        $bestMove = $this->bestMove($this->computer, $this->opponent, $moveResult)[1];
        if ($bestMove->getFrom() !== [] && $bestMove->getTo() !== []) {
            return $this->game->run($startCondition, $bestMove, $logger);
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
     * @return array<Move>
     */
    public function getPossibleMoves(array $board, PlayerInterface $player): array
    {
        return $this->game->getPossibleMoves($board, $player);
    }

    public function makeMove(MoveResult $startCondition, Move $move): MoveResult
    {
        return $this->game->run($startCondition, $move);
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
                } elseif ($cell > 0) {
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
}
