<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\Game\CheckerDesk;
use App\Service\Game\Game;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\White;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GameTest extends TestCase
{
    private Game $game;
    private White $white;
    private Black $black;
    /**
     * @var array[]
     */
    private array $desk;

    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->white = new White(1, 'Roma');
        $this->black = new Black(2, 'Olena');
        $this->desk = CheckerDesk::START_DESK;
        $this->game = new Game($this->white, $this->black, $logger);
    }

    public function testRun(): void
    {
        $this->assertEquals(0, $this->desk[1][3]);

        $updatedDesk = $this->game->makeMove($this->desk, 'a3', 'b4', true);

        $this->assertEquals(0, $updatedDesk[0][2]);
        $this->assertEquals(1, $updatedDesk[1][3]);
    }

    public function testRunInFalseDirection(): void
    {
        $updatedDesk = $this->game->makeMove($this->desk, 'a3', 'b4', true);
        $updatedDesk = $this->game->makeMove($updatedDesk, 'b4', 'a3', true);

        $this->assertEquals(0, $updatedDesk[0][2]);
        $this->assertEquals(1, $updatedDesk[1][3]);
    }

    public function testRunWithoutPossibility(): void
    {
        $updatedDesk = $this->game->makeMove($this->desk, 'a3', 'c5', true);

        $this->assertEquals(1, $updatedDesk[0][2]);
        $this->assertEquals(0, $updatedDesk[2][4]);
    }
}
