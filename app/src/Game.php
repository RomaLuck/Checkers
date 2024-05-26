<?php

namespace Src;

use Psr\Log\LoggerInterface;
use Src\Figure\Checker;
use Src\Figure\FigureInterface;
use Src\Figure\King;
use Src\Helpers\LoggerFactory;
use Src\Team\Black;
use Src\Team\TeamPlayerInterface;
use Src\Team\White;

class Game
{
    /**
     * @var array[]
     */
    private array $desk;
    private LoggerInterface $logger;
    private Rules $rules;
    private TeamPlayerInterface $white;
    private TeamPlayerInterface $black;

    public function __construct(TeamPlayerInterface $white, TeamPlayerInterface $black)
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

        $selectedTeamNumber = $this->desk[$cellFrom[0]][$cellFrom[1]];
        $figure = $this->initFigure($selectedTeamNumber);

        $player->setFigure($figure);
        $this->rules->setPlayer($player);
        $this->rules->setDesk($this->desk);

        if ($this->rules->check($cellFrom, $cellTo)) {
            $this->desk[$cellFrom[0]][$cellFrom[1]] = 0;
            $this->desk[$cellTo[0]][$cellTo[1]] = $selectedTeamNumber;
            $this->logger->info("{$player->getName()} moved from cell [$from] to cell [$to]");
            $_SESSION['desk'] = $this->desk;
        } else {
            $this->logger->error('Something went wrong. Follow the rules!');
        }
    }

    public function initPlayer(array $from): TeamPlayerInterface
    {
        $selectedTeamNumber = $this->desk[$from[0]][$from[1]];
        if ($selectedTeamNumber > 0) {
            if (in_array($selectedTeamNumber, White::WHITE_NUMBERS)) {
                return $this->white;
            }
            if (in_array($selectedTeamNumber, Black::BLACK_NUMBERS)) {
                return $this->black;
            }
        }
        throw new \RuntimeException('Can not find player on this cell');
    }

    public function initFigure(int $selectedTeamNumber): FigureInterface
    {
        if (in_array($selectedTeamNumber, Checker::CHECKER_NUMBERS)) {
            return new Checker();
        }
        if (in_array($selectedTeamNumber, King::KING_NUMBERS)) {
            return new King();
        }
        throw new \RuntimeException('Figure is not selected');
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
                if (in_array($cell, White::WHITE_NUMBERS)) {
                    $whiteCheckers++;
                } elseif (in_array($cell, Black::BLACK_NUMBERS)) {
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