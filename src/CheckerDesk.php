<?php

namespace CheckersOOP\src;

use CheckersOOP\db\DbObject;

final class CheckerDesk
{
    public DbObject $object;
    private array $startWhite = ['a1', 'a3', 'b2', 'c1', 'c3', 'd2', 'e1', 'e3', 'f2', 'g1', 'g3', 'h2'];
    private array $startBlack = ['a7', 'b8', 'b6', 'c7', 'd8', 'd6', 'e7', 'f8', 'f6', 'g7', 'h8', 'h6'];
    // private $gorizontalSideDesk = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
    // private $verticalSideDesk = [1, 2, 3, 4, 5, 6, 7, 8]; 

    public function __construct(DbObject $object)
    {
        $this->object = $object;
    }

    public function fillTheTable(): void
    {
        $this->fillTableByPieces();
        $this->fillByWhite();
        $this->fillByBlack();
    }

    public function clearTable(): void
    {
        $this->object->deleteAll();
    }

    public function fillTableByPieces(): void
    {

        for ($i = 1; $i <= 8; $i++) {
            $this->object->insertItems('a' . $i, '');
            $this->object->insertItems('b' . $i, '');
            $this->object->insertItems('c' . $i, '');
            $this->object->insertItems('d' . $i, '');
            $this->object->insertItems('e' . $i, '');
            $this->object->insertItems('f' . $i, '');
            $this->object->insertItems('g' . $i, '');
            $this->object->insertItems('h' . $i, '');
        }
    }

    public function fillByWhite(): void
    {
        foreach ($this->startWhite as $i) {
            $this->object->updateItems('team', 'white', 'id', $i);
        }
    }

    public function fillByBlack(): void
    {
        foreach ($this->startBlack as $i) {
            $this->object->updateItems('team', 'black', 'id', $i);
        }
    }
}
