<?php

namespace App\Service\Game\Chess;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Figure\FigureIds;
use App\Service\Game\Chess\MoveStrategy\BishopMoveStrategy;
use App\Service\Game\Chess\Rule\IsAvailableCellFromRule;
use App\Service\Game\Chess\Rule\IsAvailableCellToRule;
use App\Service\Game\Chess\Rule\IsFirstStepForPawnMoveRule;
use App\Service\Game\Chess\Rule\IsForBeatCellToRule;
use App\Service\Game\Chess\Rule\RuleInterface;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class MoveValidator
{
    public function __construct(
        private TeamInterface $team,
        private BoardAbstract $board,
        private LoggerInterface $logger = new NullLogger()
    ) {
    }

    public function isValid(Move $move): bool
    {
        $team = $this->team;
        $board = $this->board;
        $figure = $team->getFigure();
        $from = $move->getFrom();
        $to = $move->getTo();

        $possibleMoves = [];
        foreach ($figure->getMoveStrategies() as $moveStrategy) {
            $possibleMoves = array_merge($possibleMoves, $moveStrategy->getPossibleMoves($from));
        }

        if ($figure->getId() === FigureIds::PAWN) {
            if ((new IsForBeatCellToRule())->check($team, $move, $board)) {
                $possibleMoves = array_merge($possibleMoves, (new BishopMoveStrategy())->getPossibleMoves($from));
            }
            if ((new IsFirstStepForPawnMoveRule())->check($team, $move, $board)) {
                $figure->setStep(2);
            }
        }

        if (!in_array($to, $possibleMoves)) {
            $this->logger->warning('Figure has another move strategy');

            return false;
        }

        $figureRules = $figure->getFigureRules();

        $rules = array_merge($figureRules, $this->getGeneralRules());

        foreach ($rules as $rule) {
            if (!$rule->check($team, $move, $board)) {
                $this->logger->warning($rule->getMessage());

                return false;
            }
        }

        return true;
    }

    /**
     * @return RuleInterface[]
     */
    public function getGeneralRules(): array
    {
        return [
            new IsAvailableCellFromRule(),
            new IsAvailableCellToRule(),
        ];
    }
}
