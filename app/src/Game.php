<?php

namespace Src;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Src\Figure\Checker;
use Src\Figure\FigureInterface;
use Src\Figure\King;
use Src\Helpers\LoggerFactory;
use Src\Team\Black;
use Src\Team\TeamPlayerInterface;
use Src\Team\White;

class Game
{
    public const WHITE_QUEUE = 1;
    public const BLACK_QUEUE = -1;
    /**
     * @var array[]
     */
    private array $desk;
    private LoggerInterface $logger;
    private Rules $rules;
    private TeamPlayerInterface $white;
    private TeamPlayerInterface $black;
    private int $queue;

    public function __construct(TeamPlayerInterface $white, TeamPlayerInterface $black)
    {
        if (isset($_SESSION['desk'])) {
            $this->desk = $_SESSION['desk'];
        } else {
            $this->desk = CheckerDesk::initDesk();
        }

        if (isset($_SESSION['queue'])) {
            $this->queue = $_SESSION['queue'];
        } else {
            $this->queue = self::WHITE_QUEUE;
        }

        $this->logger = LoggerFactory::getLogger('checkers');
        $this->rules = new Rules($this->logger);
        $this->white = $white;
        $this->black = $black;
    }

    public function run(string $from, string $to): void
    {
        try {
            $cellFrom = $this->transformInputData($from);
            $cellTo = $this->transformInputData($to);
            $player = $this->initPlayer($cellFrom);
        } catch (RuntimeException $e) {
            $this->logger->warning($e->getMessage());
            return;
        }

        $selectedTeamNumber = $this->getSelectedTeamNumber($cellFrom);
        $figure = $this->initFigure($selectedTeamNumber);

        $player->setFigure($figure);
        $this->rules->setPlayer($player);
        $this->rules->setDesk($this->desk);

        $figuresForBeat = $this->rules->findFiguresForBeat($cellFrom, $cellTo);
        if ($this->rules->checkForBeat($cellFrom, $cellTo) && count($figuresForBeat) > 0) {
            $this->clearCells($figuresForBeat);
            $this->updateDesk($cellFrom, $cellTo, $selectedTeamNumber);
            $this->logger->info("{$player->getName()} : [$from] => [$to]");
        } elseif ($this->rules->checkForMove($cellFrom, $cellTo)) {
            $this->updateDesk($cellFrom, $cellTo, $selectedTeamNumber);
            $this->logger->info("{$player->getName()} : [$from] => [$to]");
        } else {
            return;
        }
        if ($this->isGameOver()) {
            $this->logger->info('GAME OVER');
            return;
        }

        $this->updateFigures();
        $_SESSION['desk'] = $this->desk;
        $_SESSION['queue'] = $this->queue * -1;
    }

    public function updateFigures(): void
    {
        $whiteChecker = current(array_intersect(White::WHITE_NUMBERS, Checker::CHECKER_NUMBERS));
        $blackChecker = current(array_intersect(Black::BLACK_NUMBERS, Checker::CHECKER_NUMBERS));
        $whiteKing = current(array_intersect(White::WHITE_NUMBERS, King::KING_NUMBERS));
        $blackKing = current(array_intersect(Black::BLACK_NUMBERS, King::KING_NUMBERS));

        foreach ($this->desk as $rowKey => $row) {
            if ($row[0] === $blackChecker) {
                $this->desk[$rowKey][0] = $blackKing;
            }
            if ($row[7] === $whiteChecker) {
                $this->desk[$rowKey][7] = $whiteKing;
            }
        }
    }

    public function initPlayer(array $from): TeamPlayerInterface
    {
        $selectedTeamNumber = $this->getSelectedTeamNumber($from);
        if ($selectedTeamNumber > 0) {
            if (in_array($selectedTeamNumber, White::WHITE_NUMBERS, true)) {
                return $this->white;
            }
            if (in_array($selectedTeamNumber, Black::BLACK_NUMBERS, true)) {
                return $this->black;
            }
        }
        throw new RuntimeException('Can not find player on this cell');
    }

    public function initFigure(int $selectedTeamNumber): FigureInterface
    {
        if (in_array($selectedTeamNumber, Checker::CHECKER_NUMBERS)) {
            return new Checker();
        }
        if (in_array($selectedTeamNumber, King::KING_NUMBERS)) {
            return new King();
        }
        throw new RuntimeException('Figure is not selected');
    }

    public function transformInputData(string $cell): array
    {
        if (preg_match('!^(?<letter>[[:alpha:]]+)(?<number>\d+)$!iu', $cell, $splitCell)) {
            $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

            foreach ($letters as $key => $letter) {
                if ($letter === $splitCell['letter'] && $splitCell['number'] > 0 && $splitCell['number'] <= 8) {
                    return [$key, $splitCell['number'] - 1];
                }
            }
            throw new RuntimeException('Cell is unavailable');
        }
        throw new RuntimeException('Cell is incorrect');
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
                if (in_array($cell, White::WHITE_NUMBERS, true)) {
                    $whiteCheckers++;
                } elseif (in_array($cell, Black::BLACK_NUMBERS, true)) {
                    $blackCheckers++;
                }
            }
        }

        return $whiteCheckers === 0 || $blackCheckers === 0;
    }

    public function clearCells(array $figuresForBeat): void
    {
        foreach ($figuresForBeat as $cell) {
            $this->desk[$cell[0]][$cell[1]] = 0;
        }
    }

    public function updateDesk(array $cellFrom, array $cellTo, int $selectedTeamNumber): void
    {
        $this->desk[$cellFrom[0]][$cellFrom[1]] = 0;
        $this->desk[$cellTo[0]][$cellTo[1]] = $selectedTeamNumber;
    }

    public function getSelectedTeamNumber(array $cellFrom): mixed
    {
        return $this->desk[$cellFrom[0]][$cellFrom[1]];
    }

    public function getQueue(): int
    {
        return $this->queue;
    }
}