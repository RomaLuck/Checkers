<?php

namespace App\Db;

class CheckerObject
{
    private int $id;
    private string $cell;
    private string $team;
    private string $figure;

    public function __construct(array $data)
    {
        $this->setData($data);
    }

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

    public function setData(array $data): void
    {
        if (!is_iterable($data)) {
            throw new \RuntimeException('Input data is not iterable');
        }
        foreach ($data as $item => $value) {
            $this->$item = $value ?? '';
        }
    }
}