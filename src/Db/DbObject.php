<?php

namespace App\Db;

class DbObject
{

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function initializeDataFields(): self
    {
        foreach ($this->data as $item => $value) {
            $this->$item = $value ?? '';
        }
        return $this;
    }

    public function filterByField(string $field): array
    {
        return array_column($this->data, $field);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}