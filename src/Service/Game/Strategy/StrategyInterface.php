<?php

namespace App\Service\Game\Strategy;

use App\Entity\GameLaunch;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

interface StrategyInterface
{
    public function run(GameLaunch $gameLaunch, string $roomId, Request $request, LoggerInterface $logger): void;
}