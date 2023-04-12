<?php

namespace CheckersOOP\src;

use CheckersOOP\db\DbObject;

final class CheckerDesk extends DbObject
{
    public $tableName = 'CheckerDesk';
    // private $gorizontalSideDesk = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
    // private $verticalSideDesk = [1, 2, 3, 4, 5, 6, 7, 8]; 

    public function __construct($db)
    {
        parent::__construct($db);
        $this->setTableName($this->tableName);
    }

    public function fillTableByPieces()
    {

        for ($i = 1; $i <= 8; $i++) {
            $this->insertItem('id','a' . $i);
            $this->insertItem('id','b' . $i);
            $this->insertItem('id','c' . $i);
            $this->insertItem('id','d' . $i);
            $this->insertItem('id','e' . $i);
            $this->insertItem('id','f' . $i);
            $this->insertItem('id','g' . $i);
            $this->insertItem('id','h' . $i);
        }
    } 
}

