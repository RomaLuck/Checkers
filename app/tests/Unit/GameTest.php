<?php

namespace App\Tests\Unit;

use App\Service\Game\CheckerDesk;
use App\Service\Game\Game;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\White;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use function PHPUnit\Framework\assertEquals;

class GameTest extends TestCase
{
    private Game $game;
    private White $white;
    private Black $black;

    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->white = new White(1, 'Roma');
        $this->black = new Black(2, 'Olena');
        $this->game = new Game(new CheckerDesk(CheckerDesk::START_DESK), $this->white, $this->black, $logger);
    }

    public function testRun(): void
    {
        $this->game->run('a3', 'b4');

        $updatedDesk = $this->game->getDesk()->getDeskData();
        $this->assertEquals(0, $updatedDesk[0][2]);
        $this->assertEquals(1, $updatedDesk[1][3]);
    }

    public function testRunInFalseDirection(): void
    {
        $this->game->run('a3', 'b4');
        $this->game->run('b4', 'a3');

        $updatedDesk = $this->game->getDesk()->getDeskData();
        $this->assertEquals(0, $updatedDesk[0][2]);
        $this->assertEquals(1, $updatedDesk[1][3]);
    }

    public function testRunWithoutPossibility(): void
    {
        $this->game->run('a3', 'c5');

        $updatedDesk = $this->game->getDesk()->getDeskData();
        assertEquals(1, $updatedDesk[0][2]);
        assertEquals(0, $updatedDesk[2][4]);
    }
}
