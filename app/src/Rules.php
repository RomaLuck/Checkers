<?php

namespace Src;

use Psr\Log\LoggerInterface;
use Src\Teams\TeamPlayer;

class Rules
{
    private array $desk;
    private array $from;
    private array $to;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function check(TeamPlayer $player, array $desk, array $from, array $to): bool
    {
        $this->desk = $desk;
        $this->from = $from;
        $this->to = $to;

        return true;
    }

    public function isAvailable(array $to): bool
    {
        if ($this->desk[$to[0]][$to[1]] === 0) {
            return true;
        }

        $this->logger->error('Cell is not available');
        return false;
    }

}