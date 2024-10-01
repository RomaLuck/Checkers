<?php

namespace App\Service\Game\Chess\Team;

use App\Service\Game\Chess\Figure\FigureInterface;

abstract class Team implements TeamInterface
{
    private FigureInterface $figure;

    private string $name;

    private int $id;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFigure(): FigureInterface
    {
        return $this->figure;
    }

    public function setFigure(FigureInterface $figure): void
    {
        $this->figure = $figure;
    }
}