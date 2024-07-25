<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Rule\IsAvailableCellRule;
use App\Service\Game\Rule\IsCorrectStep;
use App\Service\Game\Rule\IsOpportunityForBeatRule;
use App\Service\Game\Rule\IsOpportunityForMoveRule;
use App\Service\Game\Rule\IsTrueDirectionRule;
use App\Service\Game\Team\PlayerInterface;
use Psr\Log\LoggerInterface;

final class Rules
{
    public function __construct(
        private PlayerInterface  $player,
        private array            $desk,
        private ?LoggerInterface $logger
    ) {
    }

    /**
     * @param array<int> $from
     * @param array<int> $to
     */
    public function checkForMove(array $from, array $to): bool
    {
        $rules = [
            new IsAvailableCellRule($this->desk),
            new IsTrueDirectionRule(),
            new IsCorrectStep(),
            new IsOpportunityForMoveRule(),
        ];

        foreach ($rules as $rule) {
            if (!$rule->check($this->player, $from, $to)) {
                $this->logger?->warning($rule->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<int> $from
     * @param array<int> $to
     */
    public function checkForBeat(array $from, array $to): bool
    {
        $rules = [
            new IsAvailableCellRule($this->desk),
            new IsCorrectStep(),
            new IsOpportunityForBeatRule(),
        ];

        foreach ($rules as $rule) {
            if (!$rule->check($this->player, $from, $to)) {
                $this->logger?->warning($rule->getMessage());
                return false;
            }
        }

        return true;
    }
}
