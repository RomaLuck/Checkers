<?php

namespace App\Tests\Unit;

use App\Service\Game\Figure\Checker;
use App\Service\Game\Figure\FigureFactory;
use App\Service\Game\Figure\King;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FigureFactoryTest extends TestCase
{
    public function testCreateFigure(): void
    {
        $factory = new FigureFactory(1);
        $this->assertInstanceOf(Checker::class, $factory->create());
        $factory = new FigureFactory(4);
        $this->assertInstanceOf(King::class, $factory->create());
    }

    public function createFalseFigure(): void
    {
        $this->expectException(RuntimeException::class);
        $figure = (new FigureFactory(5))->create();
    }
}
