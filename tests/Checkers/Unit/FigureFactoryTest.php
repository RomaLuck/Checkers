<?php

declare(strict_types=1);

namespace App\Tests\Checkers\Unit;

use App\Service\Game\Checkers\Figure\Checker;
use App\Service\Game\Checkers\Figure\FigureFactory;
use App\Service\Game\Checkers\Figure\King;
use PHPUnit\Framework\TestCase;

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
        $this->expectException(\RuntimeException::class);
        $figure = (new FigureFactory(5))->create();
    }
}
