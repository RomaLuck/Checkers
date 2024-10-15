<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Figure;

class FigureFactory
{
    public static function create(int $figureId): FigureInterface
    {
        return match (true) {
            in_array($figureId, FigureIds::ROOK) => new Rook(),
            in_array($figureId, FigureIds::QUEEN) => new Queen(),
            in_array($figureId, FigureIds::PAWN) => new Pawn(),
            in_array($figureId, FigureIds::KING) => new King(),
            in_array($figureId, FigureIds::KNIGHT) => new Knight(),
            in_array($figureId, FigureIds::BISHOP) => new Bishop(),
            default => throw new \RuntimeException('Figure is not selected'),
        };
    }
}
