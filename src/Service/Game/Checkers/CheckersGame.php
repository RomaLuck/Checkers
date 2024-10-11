<?php

declare(strict_types=1);

namespace App\Service\Game\Checkers;

use App\Service\Game\Checkers\Figure\FigureFactory;
use App\Service\Game\Checkers\Team\Black;
use App\Service\Game\Checkers\Team\PlayerDetector;
use App\Service\Game\Checkers\Team\PlayerInterface;
use App\Service\Game\Checkers\Team\White;
use App\Service\Game\GameTypeInterface;
use App\Service\Game\InputTransformer;
use App\Service\Game\Move;
use App\Service\Game\MoveResult;
use Psr\Log\LoggerInterface;

final class CheckersGame implements GameTypeInterface
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

    public function run(
        MoveResult       $currentCondition,
        Move             $move,
        ?LoggerInterface $logger = null
    ): MoveResult
    {
        $desk = $currentCondition->getCheckerDesk();
        $currentTurn = $currentCondition->getCurrentTurn();

        $playerDetector = new PlayerDetector($this->white, $this->black);
        $selectedTeamNumber = CheckerDeskService::getSelectedTeamNumber($desk, $move->getFrom());

        $player = $playerDetector->detect($selectedTeamNumber);
        if (!$player) {
            $logger?->warning('Can not find player on this cell');
            return $currentCondition;
        }

        $figure = FigureFactory::create($selectedTeamNumber);
        $player->setFigure($figure);

        if (!$player->isTurnForPlayer($currentTurn)) {
            $logger?->warning('Now it\'s the turn of another player');
            return $currentCondition;
        }

        if (!$this->isValidMove($player, $desk, $move, $logger)) {
            return $currentCondition;
        }

        $figuresForBeat = $this->checkerDeskService->findFiguresForBeat($player, $desk, $move);
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

        $from = $this->inputTransformer->transformCellToString($move->getFrom());
        $to = $this->inputTransformer->transformCellToString($move->getTo());
        $logger?->info("{$player->getName()} : [{$from}] => [{$to}]");

        if ($this->isGameOver($desk)) {
            $logger?->info('Game over');
            $advantagePlayer = $this->getAdvantagePlayer($desk);
            $winner = $advantagePlayer->getName();
            $logger?->info("{$winner} won!");

            $currentCondition->setWinnerId($advantagePlayer->getId());
        }

        $currentCondition->setCheckerDesk($this->checkerDeskService->updateData($desk, $move));
        $currentCondition->setCurrentTurn(!$currentTurn);

        return $currentCondition;
    }

    /**
     * @param array<array<int>> $desk
     */
    public function isGameOver(array $desk): bool
    {
        return $this->countFigures($desk, White::WHITE_NUMBERS) === 0
            || $this->countFigures($desk, Black::BLACK_NUMBERS) === 0
            || $this->getPossibleMoves($desk, $this->white) === []
            || $this->getPossibleMoves($desk, $this->black) === [];
    }

    /**
     * @param array<array<int>> $desk
     */
    public function getAdvantagePlayer(array $desk): PlayerInterface
    {
        return $this->countFigures($desk, White::WHITE_NUMBERS)
        > $this->countFigures($desk, Black::BLACK_NUMBERS)
            ? $this->white
            : $this->black;
    }

    /**
     * @param array<array<int>> $board
     *
     * @return array<Move>
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
                    $move = new Move($from, $destination);
                    $selectedTeamNumber = CheckerDeskService::getSelectedTeamNumber($board, $move->getFrom());
                    $figure = FigureFactory::create($selectedTeamNumber);
                    $player->setFigure($figure);
                    if ($this->isValidMove($player, $board, $move)) {
                        $possibleMoves[] = $move;
                    }
                }
            }
        }

        return $possibleMoves;
    }

    public function isValidMove(
        PlayerInterface  $player,
        array            $desk,
        Move             $move,
        ?LoggerInterface $logger = null
    ): bool
    {
        $rules = new Rules($player, $desk, $logger);

        $figuresForBeat = $this->checkerDeskService->findFiguresForBeat($player, $desk, $move);
        if (count($figuresForBeat) > 0) {
            if ($rules->checkForBeat($move)) {
                return true;
            }

            return false;
        }

        if ($rules->checkForMove($move)) {
            return true;
        }

        return false;
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

    /**
     * @param array<array<int>> $desk
     * @param array<int> $figureNumbers
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
