<?php

namespace App\Db;

class DbObject
{

    public function __construct(array $data)
    {
        $this->setData($data);
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