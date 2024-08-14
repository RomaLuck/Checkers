<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\CheckerDeskService;
use App\Service\Game\Team\PlayerInterface;

final class IsOpportunityForBeatRule implements RuleInterface
{
    public function __construct(private array $desk)
    {
    }

    public function check(PlayerInterface $player, array $from, array $to): bool
    {
        $step = abs($to[1] - $from[1]);

        $deskService = new CheckerDeskService();
        $figuresForBeat = $deskService->findFiguresForBeat($player, $this->desk, $from, $to);

        $stepLengthBetweenCheckersList = [];
        for ($i = 1; $i < count($figuresForBeat); $i++) {
            $stepLengthBetweenCheckersList[] = abs($figuresForBeat[$i][1] - $figuresForBeat[$i - 1][1]);
        }

        return $step <= $player->getFigure()->getStepOpportunityForAttack()
            && ! in_array(1, $stepLengthBetweenCheckersList);
    }

    public function getMessage(): string
    {
        return 'You do not have ability to reach this cell';
    }
}
