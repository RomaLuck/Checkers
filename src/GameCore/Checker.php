<?php

namespace App\GameCore;

use App\Db\CheckerObjectRepository;

class Checker extends FigureType
{
    public function __construct(CheckerObjectRepository $repository)
    {
        parent::__construct($repository);
        $this->moveOpportunity = 1;
        $this->figureName = FigureType::CHECKER;
    }
}