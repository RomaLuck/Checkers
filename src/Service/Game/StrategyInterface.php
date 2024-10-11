<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Entity\GameLaunch;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

interface StrategyInterface
{
    public function run(GameLaunch $gameLaunch, string $roomId, Request $request, LoggerInterface $logger): void;
}
