<?php

namespace Src;

use Psr\Log\LoggerInterface;
use Src\Helpers\LoggerFactory;
use Src\Teams\Black;
use Src\Teams\TeamPlayer;
use Src\Teams\White;

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
       if (isset($_SESSION['desk'])) {
            $this->desk = $_SESSION['desk'];
        } else {
            $this->desk = CheckerDesk::initDesk();
        }

        $this->logger = LoggerFactory::getLogger('checkers');
        $this->rules = new Rules($this->logger);
        $this->white = $white;
        $this->black = $black;
    }

    public function run(string $from, string $to): void
    {
        try {
            $cellFrom = $this->transform($from);
            $cellTo = $this->transform($to);
            $player = $this->initPlayer($cellFrom);
        } catch (\RuntimeException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        if ($this->rules->check($player, $this->desk, $cellFrom, $cellTo)) {
            $selectedTeamNumber = $this->desk[$cellFrom[0]][$cellFrom[1]];
            $this->desk[$cellFrom[0]][$cellFrom[1]] = 0;
            $this->desk[$cellTo[0]][$cellTo[1]] = $selectedTeamNumber;
            $this->logger->info("{$player->getName()} moved from cell [$from] to cell [$to]");
            $_SESSION['desk'] = $this->desk;
        } else {
            $this->logger->error('Something was wrong');
        }
    }

    public function initPlayer(array $from): TeamPlayer
    {
        $selectedTeamNumber = $this->desk[$from[0]][$from[1]];
        if ($selectedTeamNumber > 0) {
            if ($selectedTeamNumber === White::WHITE_CHECKER) {
                return $this->white;
            }
            if ($selectedTeamNumber === Black::BLACK_CHECKER) {
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

    public function getDesk(): array
    {
        return $this->desk;
    }

    public function isGameOver(): bool
    {
        $whiteCheckers = 0;
        $blackCheckers = 0;

        foreach ($this->desk as $row) {
            foreach ($row as $cell) {
                if ($cell === White::WHITE_CHECKER) {
                    $whiteCheckers++;
                } elseif ($cell === Black::BLACK_CHECKER) {
                    $blackCheckers++;
                }
            }
        }

        return $whiteCheckers === 0 || $blackCheckers === 0;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}