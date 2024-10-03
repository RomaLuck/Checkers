<?php

namespace App\Tests\Unit\Chess\Figures;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\ChessBoard;
use App\Service\Game\Chess\Figure\Bishop;
use App\Service\Game\Chess\Figure\FigureInterface;
use App\Service\Game\Chess\MoveValidator;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Chess\Team\White;
use App\Service\Game\Move;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

abstract class MoveValidationTestAbstract extends TestCase
{
    protected TeamInterface $team;

    protected BoardAbstract $board;

    protected function setUp(): void
    {
        $this->team = new White(1, 'Roman');
        $this->board = new ChessBoard($this->getStartDesk());
        $this->team->setFigure($this->getFigure());
    }

    /**
     * @dataProvider getStepDataProvider
     */
    public function testMoveValidation(bool $expected, array $from, array $to): void
    {
        $validator = new MoveValidator($this->team, $this->board, new NullLogger());
        $move = new Move($from, $to);
        self::assertEquals($expected, $validator->isValid($move));
    }

    protected static abstract function getStepDataProvider(): array;

    protected abstract function getStartDesk(): array;

    protected abstract function getFigure(): FigureInterface;
}