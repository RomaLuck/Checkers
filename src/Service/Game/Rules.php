<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Checkers\Rule\IsAvailableCellRule;
use App\Service\Game\Checkers\Rule\IsCorrectStep;
use App\Service\Game\Checkers\Rule\IsOpportunityForBeatRule;
use App\Service\Game\Checkers\Rule\IsOpportunityForMoveRule;
use App\Service\Game\Checkers\Rule\IsTrueDirectionRule;
use App\Service\Game\Checkers\Rule\RuleInterface;
use App\Service\Game\Team\PlayerInterface;
use Psr\Log\LoggerInterface;

final class Rules
{
    /**
     * @param array<array<int>> $desk
     */
    public function __construct(
        private PlayerInterface $player,
        private array $desk,
        private ?LoggerInterface $logger
    ) {
    }

    public function checkForMove(Move $move): bool
    {
        $rules = [
            new IsAvailableCellRule($this->desk),
            new IsTrueDirectionRule(),
            new IsCorrectStep(),
            new IsOpportunityForMoveRule(),
        ];

        return $this->checkRules($rules, $move);
    }

    public function checkForBeat(Move $move): bool
    {
        $rules = [
            new IsAvailableCellRule($this->desk),
            new IsCorrectStep(),
            new IsOpportunityForBeatRule($this->desk),
        ];

        return $this->checkRules($rules, $move);
    }

    /**
     * @param array<RuleInterface> $rules
     */
    public function checkRules(array $rules, Move $move): bool
    {
        foreach ($rules as $rule) {
            if (!$rule->check($this->player, $move)) {
                $this->logger?->warning($rule->getMessage());

                return false;
            }
        }

        return true;
    }
}
