<?php

namespace CheckersOOP\src;

use CheckersOOP\db\Database;
use CheckersOOP\db\DbObject;
use CheckersOOP\src\RuleChecker;

class Player extends DbObject
{
    public $chooseFigure;
    public $setStep;
    private $rules;
    public $teamName;
    public $tableName;
    public $ruleChecker;
    public $moveOpportunity = 1;
    public $moveDirection = 1;
    public $desk;

    public function __construct($db, $tableName)
    {
        parent::__construct($db);
        $this->tableName = $tableName;
        $this->teamName = $tableName;
        $this->desk = new CheckerDesk($db);
    }

    public function move($chooseFigure, $setStep)
    {
        $this->chooseFigure = $chooseFigure;
        $this->setStep = $setStep;
        if ($this->rules->checkForMove()) {
            $this->insertItem('pieceName', $this->setStep);
            $this->desk->updateItem('Team', $this->teamName, $this->setStep);
            $this->deleteItem('pieceName', $this->chooseFigure);
            $this->desk->updateItem('Team', null, $this->chooseFigure);
        } else {
            echo 'you can not move this item';
        }
    }

    public function setRules(RuleChecker $rules)
    {
        $this->rules = $rules;
    }
}
