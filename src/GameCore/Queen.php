<?php

namespace App\GameCore;

use App\Db\CheckerObjectRepository;

class Queen extends FigureType
{
    public function __construct(CheckerObjectRepository $repository)
    {
        parent::__construct($repository);
        $this->moveOpportunity = 100;
        $this->figureName = FigureType::QUEEN;
    }
}