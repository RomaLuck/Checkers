<?php

namespace App\Tests\Unit;

use App\Service\Game\CheckerDesk;
use App\Service\Game\CheckerDeskService;
use App\Service\Game\Move;
use PHPUnit\Framework\TestCase;

class CheckerDeskServiceTest extends TestCase
{
    /**
     * @var array<array>
     */
    private array $desk;

    private CheckerDeskService $checkerDeskService;

    protected function setUp(): void
    {
        $this->desk = CheckerDesk::START_DESK;
        $this->checkerDeskService = new CheckerDeskService();
    }

    public function testGetSelectedTeamNumber(): void
    {
        $teamNumber = $this->checkerDeskService->getSelectedTeamNumber($this->desk, [0, 0]);
        $this->assertEquals(1, $teamNumber);
    }

    public function testClearCells(): void
    {
        $updatedDesk = $this->checkerDeskService->clearCells($this->desk, [[0, 0]]);
        $teamNumber = $this->checkerDeskService->getSelectedTeamNumber($updatedDesk, [0, 0]);
        $this->assertEquals(0, $teamNumber);
    }

    public function testUpdateDesk(): void
    {
        $move = new Move([0, 2], [1, 3]);
        $updatedDesk = $this->checkerDeskService->updateDesk($this->desk, $move, 1);
        $teamNumberOld = $this->checkerDeskService->getSelectedTeamNumber($updatedDesk, [0, 2]);
        $teamNumberNew = $this->checkerDeskService->getSelectedTeamNumber($updatedDesk, [1, 3]);
        $this->assertEquals(0, $teamNumberOld);
        $this->assertEquals(1, $teamNumberNew);
    }

    public function testUpdateFigures(): void
    {
        $desk = [
            [2, -1, 1, -1, 0, -1, 2, -1],
            [-1, 1, -1, 0, -1, 2, -1, 2],
            [1, -1, 1, -1, 0, -1, 2, -1],
            [-1, 1, -1, 0, -1, 2, -1, 2],
            [1, -1, 1, -1, 0, -1, 2, -1],
            [-1, 1, -1, 0, -1, 2, -1, 2],
            [1, -1, 1, -1, 0, -1, 2, -1],
            [-1, 1, -1, 0, -1, 2, -1, 2],
        ];
        $updatedDesk = $this->checkerDeskService->updateFigures($desk);
        $teamNumberNew = $this->checkerDeskService->getSelectedTeamNumber($updatedDesk, [0, 0]);
        $this->assertEquals(4, $teamNumberNew);
    }
}
