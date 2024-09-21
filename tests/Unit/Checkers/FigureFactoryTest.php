<?php

declare(strict_types=1);

namespace App\Tests\Unit\Checkers;

use App\Service\Game\Checkers\Figure\Checker;
use App\Service\Game\Checkers\Figure\FigureFactory;
use App\Service\Game\Checkers\Figure\King;
use PHPUnit\Framework\TestCase;

class FigureFactoryTest extends TestCase
{
    public function testCreateFigure(): void
    {
        $this->assertInstanceOf(Checker::class, FigureFactory::create(1));
        $this->assertInstanceOf(King::class, FigureFactory::create(4));
    }

    public function createFalseFigure(): void
    {
        $this->expectException(\RuntimeException::class);
        $figure = FigureFactory::create(5);
    }
}
