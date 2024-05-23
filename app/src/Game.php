<?php

namespace Src;

use Psr\Log\LoggerInterface;
use Src\Helpers\LoggerFactory;
use Src\Teams\TeamPlayer;

class Game
{
    /**
     * @var array[]
     */
    private array $desk;
    private LoggerInterface $logger;
    private Rules $rules;
    private TeamPlayer $white;
    private TeamPlayer $black;

    public function __construct(TeamPlayer $white, TeamPlayer $black)
    {
        $this->desk = CheckerDesk::initDesk();
        $this->logger = LoggerFactory::getLogger('checkers');
        $this->rules = new Rules($this->logger);
        $this->white = $white;
        $this->black = $black;
    }

    public function start(): void
    {
        //
    }

    public function move(array $desk, string $from, string $to): void
    {
        try {
            $cellFrom = $this->transform($from);
            $cellTo = $this->transform($to);
            $player = $this->initPlayer($cellFrom);
        } catch (\RuntimeException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        if ($this->rules->check($player, $desk, $cellFrom, $cellTo)) {
            $this->desk[$cellFrom[0]][$cellFrom[1]] = 0;
            $this->desk[$cellTo[0]][$cellTo[1]] = $player->getTeamNumber();
        } else {
            $this->logger->error('Something was wrong');
        }
    }

    public function initPlayer(array $from): TeamPlayer
    {
        $selectedTeamNumber = $this->desk[$from[0]][$from[1]];
        if ($selectedTeamNumber > 0) {
            if ($selectedTeamNumber === $this->white->getTeamNumber()) {
                return $this->white;
            }
            if ($selectedTeamNumber === $this->black->getTeamNumber()) {
                return $this->black;
            }
        }
        throw new \RuntimeException('Can not find player on this cell');
    }

    public function transform(string $cell): array
    {
        if (preg_match('!^(?<letter>[[:alpha:]]+)(?<number>\d+)$!iu', $cell, $splitCell)) {
            $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

            foreach ($letters as $key => $letter) {
                if ($letter === $splitCell['letter'] && $splitCell['number'] > 0 && $splitCell['number'] <= 8) {
                    return [$key, $splitCell['number'] - 1];
                }
            }
            throw new \RuntimeException('Cell is unavailable');
        }
        throw new \RuntimeException('Cell is incorrect');
    }
}