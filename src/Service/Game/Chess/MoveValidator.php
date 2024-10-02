<?php

namespace App\Service\Game\Chess;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Rule\IsAvailableCellFromRule;
use App\Service\Game\Chess\Rule\IsAvailableCellToRule;
use App\Service\Game\Chess\Rule\RuleInterface;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;
use Psr\Log\LoggerInterface;

class MoveValidator
{
    public function __construct(
        private TeamInterface   $team,
        private BoardAbstract   $board,
        private LoggerInterface $logger
    )
    {
    }

    public function isValid(Move $move): bool
    {
        $team = $this->team;
        $possibleMoves = [];
        foreach ($team->getFigure()->getMoveStrategies() as $moveStrategy) {
            $possibleMoves = array_merge($possibleMoves, $moveStrategy->getPossibleMoves($move->getFrom()));
        }

        if (!in_array($move->getTo(), $possibleMoves)) {
            $this->logger->warning('Figure has another move strategy');
            return false;
        }

        $figureRules = $team->getFigure()->getFigureRules();

        $rules = array_merge($figureRules, $this->getGeneralRules());

        foreach ($rules as $rule) {
            if (!$rule->check($team, $move, $this->board)) {
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