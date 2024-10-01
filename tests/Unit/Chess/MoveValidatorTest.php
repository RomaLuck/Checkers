<?php

namespace App\Tests\Unit\Chess;

use App\Service\Game\Chess\ChessBoard;
use App\Service\Game\Chess\Figure\Pawn;
use App\Service\Game\Chess\MoveValidator;
use App\Service\Game\Chess\Team\White;
use App\Service\Game\Move;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class MoveValidatorTest extends TestCase
{
    private White $team;
    private ChessBoard $board;

    protected function setUp(): void
    {
        $this->team = new White(1, 'Roman');
        $this->board = new ChessBoard(ChessBoard::START_DESK);
    }

    /**
     * @dataProvider getPawnStepDataProvider
     */
    public function testPawnMoveValidation(bool $expected, array $from, array $to): void
    {
        $this->team->setFigure(new Pawn());
        $validator = new MoveValidator($this->team, $this->board, new NullLogger());
        $move = new Move($from, $to);
        $result = $validator->isValid($move);
        self::assertEquals($expected, $result);
    }

    public static function getPawnStepDataProvider(): array
    {
        return [
            [true, [1, 1], [1, 2]],
            [false, [1, 1], [1, 3]],
            [false, [1, 1], [2, 2]],
            [false, [2, 2], [2, 3]],
            [false, [1, 1], [2, 1]],
            [false, [1, 1], [0, 0]],
        ];
    }
}