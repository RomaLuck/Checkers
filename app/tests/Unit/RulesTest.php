<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\Game\CheckerDesk;
use App\Service\Game\Figure\FigureInterface;
use App\Service\Game\Rules;
use App\Service\Game\Team\PlayerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class RulesTest extends TestCase
{
    private Rules $rules;

    protected function setUp(): void
    {
        $figure = $this->createMock(FigureInterface::class);
        $figure->method('getAvailableDirections')->willReturn([1]);
        $figure->method('getStepOpportunityForMove')->willReturn(1);
        $figure->method('getStepOpportunityForAttack')->willReturn(2);

        $player = $this->createMock(PlayerInterface::class);
        $player->method('getDirection')->willReturn(1);
        $player->method('getFigure')->willReturn($figure);

        $logger = $this->createMock(LoggerInterface::class);
        $this->rules = new Rules($logger);
        $this->rules->setDesk(CheckerDesk::START_DESK);
        $this->rules->setPlayer($player);
    }

    /**
     * @dataProvider getStepDataProvider
     */
    public function testCheckForMove(bool $expected, array $from, array $to): void
    {
        $this->assertEquals($expected, $this->rules->checkForMove($from, $to));
    }

    /**
     * @dataProvider getStepDataProvider
     */
    public function testCheckForAttack(bool $expected, array $from, array $to): void
    {
        $this->assertEquals($expected, $this->rules->checkForBeat($from, $to));
    }

    public static function getStepDataProvider(): array
    {
        return [
            [true, [0, 2], [1, 3]],
            [false, [0, 2], [0, 3]],
            [false, [0, 2], [1, 4]],
            [false, [0, 2], [1, 1]],
        ];
    }
}
