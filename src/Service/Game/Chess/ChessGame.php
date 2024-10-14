<?php

namespace App\Service\Game\Chess;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Figure\FigureFactory;
use App\Service\Game\Chess\Team\Black;
use App\Service\Game\Chess\Team\TeamDetector;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Chess\Team\White;
use App\Service\Game\GameTypeInterface;
use App\Service\Game\Move;
use App\Service\Game\MoveResult;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ChessGame implements GameTypeInterface
{
    public function __construct(
        private White $white,
        private Black $black,
        private LoggerInterface $logger = new NullLogger()
    ) {
    }

    public function run(MoveResult $currentCondition, Move $move): MoveResult
    {
        $board = new ChessBoard($currentCondition->getCheckerDesk());
        $currentTurn = $currentCondition->getCurrentTurn();

        $teamNumber = $board->getFigureNumber($move->getFrom());
        if (!$teamNumber) {
            $this->logger->warning('Can not find this cell');

            return $currentCondition;
        }

        $teamDetector = new TeamDetector($this->white, $this->black);
        $team = $teamDetector->detect($teamNumber);
        if (!$team) {
            $this->logger->warning('Can not find player on this cell');

            return $currentCondition;
        }

        $figure = FigureFactory::create($teamNumber);
        $team->setFigure($figure);

        if (!$team->isTurnForTeam($currentTurn)) {
            $this->logger->warning('Now it\'s the turn of another player');

            return $currentCondition;
        }

        if (!(new MoveValidator($team, $board, $this->logger))->isValid($move)) {
            return $currentCondition;
        }

        if ($this->isGameOver($board)) {
            $this->logger->info('Game over');
            $advantagePlayer = $this->getAdvantagePlayer($board);
            $winner = $advantagePlayer->getName();
            $this->logger->info("{$winner} won!");

            $currentCondition->setWinnerId($advantagePlayer->getId());
        }

        $board->update($move);

        $currentCondition->setCheckerDesk($board->getBoardData());
        $currentCondition->setCurrentTurn(!$currentTurn);

        return $currentCondition;
    }

    public function isGameOver(BoardAbstract $board): bool
    {
        return $board->countFigures($this->white->getTeamNumbers()) === 0
            || $board->countFigures($this->black->getTeamNumbers()) === 0
            || $this->getPossibleMoves($board, $this->white) === []
            || $this->getPossibleMoves($board, $this->black) === [];
    }

    public function getAdvantagePlayer(BoardAbstract $board): TeamInterface
    {
        return $board->countFigures(White::WHITE_NUMBERS)
        > $board->countFigures(Black::BLACK_NUMBERS)
            ? $this->white
            : $this->black;
    }

    /**
     * @return array<Move>
     */
    public function getPossibleMoves(BoardAbstract $board, TeamInterface $team): array
    {
        $possibleMoves = [];

        foreach ($board->getBoardData() as $rowKey => $row) {
            foreach ($row as $key => $cell) {
                $from = [$rowKey, $key];
                if (!in_array($cell, $team->getTeamNumbers())) {
                    continue;
                }

                $possibleDestinations = $board->getEmptyCells();
                foreach ($possibleDestinations as $destination) {
                    $figureNumber = $board->getFigureNumber($from);
                    $figure = FigureFactory::create($figureNumber);
                    $team->setFigure($figure);

                    $move = new Move($from, $destination);
                    if ((new MoveValidator($team, $board))->isValid($move)) {
                        $possibleMoves[] = $move;
                    }
                }
            }
        }

        return $possibleMoves;
    }
}
