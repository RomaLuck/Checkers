<?php

declare(strict_types=1);

namespace Src;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Src\Figure\FigureFactory;
use Src\Helpers\LoggerFactory;
use Src\Team\Black;
use Src\Team\PlayerDetector;
use Src\Team\PlayerInterface;
use Src\Team\White;

final class Game
{
    public const WHITE_QUEUE = 1;
    public const UPDATE_QUEUE = -1;

    private CheckerDesk $desk;
    private LoggerInterface $logger;
    private Rules $rules;
    private PlayerDetector $playerDetector;
    private int $queue;

    public function __construct(PlayerInterface $white, PlayerInterface $black)
    {
        $deskData = $_SESSION['desk'] ?? CheckerDesk::START_DESK;
        $this->desk = new CheckerDesk($deskData);
        $this->queue = $_SESSION['queue'] ?? self::WHITE_QUEUE;
        $this->logger = LoggerFactory::getLogger('checkers');
        $this->rules = new Rules($this->getLogger());
        $this->playerDetector = new PlayerDetector($white, $black);
    }

    public function getDesk(): CheckerDesk
    {
        return $this->desk;
    }

    public function getQueue(): int
    {
        return $this->queue;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getRules(): Rules
    {
        return $this->rules;
    }

    public function getPlayerDetector(): PlayerDetector
    {
        return $this->playerDetector;
    }

    public function run(string $from, string $to): void
    {
        try {
            $cellFrom = $this->transformInputData($from);
            $cellTo = $this->transformInputData($to);
        } catch (RuntimeException $e) {
            $this->getLogger()->warning($e->getMessage());
            return;
        }

        $selectedTeamNumber = $this->getDesk()->getSelectedTeamNumber($cellFrom);
        $player = $this->getPlayerDetector()->detect($selectedTeamNumber);
        $figure = (new FigureFactory($selectedTeamNumber))->create();

        $player->setFigure($figure);
        $this->getRules()->setPlayer($player);
        $this->getRules()->setDesk($this->getDesk()->getDeskData());

        if (!$this->isValidMove($cellFrom, $cellTo)) {
            return;
        }

        $this->updateGameState($cellFrom, $cellTo, $selectedTeamNumber, $player);
        $this->getLogger()->info("{$player->getName()} : [{$from}] => [{$to}]");

        if ($this->isGameOver()) {
            $this->getLogger()->info('GAME OVER');
        }
    }

    private function isValidMove(array $cellFrom, array $cellTo): bool
    {
        $figuresForBeat = $this->getRules()->findFiguresForBeat($cellFrom, $cellTo);
        if (count($figuresForBeat) > 0 && $this->getRules()->checkForBeat($cellFrom, $cellTo)) {
            $this->getDesk()->clearCells($figuresForBeat);
            return true;
        }

        if ($this->getRules()->checkForMove($cellFrom, $cellTo)) {
            return true;
        }

        return false;
    }

    private function updateGameState(array $cellFrom, array $cellTo, int $selectedTeamNumber): void
    {
        $this->getDesk()->updateDesk($cellFrom, $cellTo, $selectedTeamNumber);
        $this->getDesk()->updateFigures();
        $_SESSION['desk'] = $this->getDesk()->getDeskData();
        $_SESSION['queue'] = $this->getQueue() * self::UPDATE_QUEUE;
    }

    /**
     * @return array<int>
     */
    public function transformInputData(string $cell): array
    {
        if (!preg_match('!^(?<letter>[[:alpha:]]+)(?<number>\d+)$!iu', $cell, $splitCell)) {
            throw new RuntimeException('Cell is incorrect');
        }

        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        $key = array_search($splitCell['letter'], $letters, true);

        if ($key === false || $splitCell['number'] <= 0 || $splitCell['number'] > 8) {
            throw new RuntimeException('Cell is unavailable');
        }

        return [$key, $splitCell['number'] - 1];
    }

    private function isGameOver(): bool
    {
        return $this->countFigures(White::WHITE_NUMBERS) === 0
            || $this->countFigures(Black::BLACK_NUMBERS) === 0;
    }

    private function countFigures(array $figureNumbers): int
    {
        $count = 0;

        foreach ($this->getDesk()->getDeskData() as $row) {
            foreach ($row as $cell) {
                if (in_array($cell, $figureNumbers, true)) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
