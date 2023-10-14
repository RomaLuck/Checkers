<?php

namespace App\Db;

class CheckerObject extends DbObject
{
    protected int $id;
    protected string $cell;
    protected string $team;
    protected string $figure;

    private function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCell(): string
    {
        return $this->cell;
    }

    public function setCell(string $cell): void
    {
        $this->cell = $cell;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(?string $team): void
    {
        $this->team = $team;
    }

    public function getFigure(): ?string
    {
        return $this->figure;
    }

    public function setFigure(?string $figure): void
    {
        $this->figure = $figure;
    }
}