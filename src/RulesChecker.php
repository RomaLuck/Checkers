<?php

class RulesChecker
{
    public $team = [];
    public $checkerDesk = [];
    public $choose_figure = ['b', 3];
    public $set_step = ['c', 2];
    public $move_rightside;
    public $move_leftside;
    public $move_up;
    public $move_down;
    public $move_direction;
    public $move_opportunity;

    public function __construct(DbObject $team)
    {
        $this->team = $team;
    }

    public function move($choose_figure, $set_step)
    {
        if ($this->checkForMove()) {
            $this->choose_figure = $choose_figure;
            $this->set_step = $set_step;
            return $this;
        } else {
            return false;
        }
    }

    public function checkForMove():bool
    {
        $this->checkTeam();
        $this->checkStep();
        $this->checkDesk();
        return true;
    }

    public function checkTeam():bool
    {
        if (array_key_exists($this->choose_figure, $this->team->showAllTeam()))
            return true;
    }


    public function checkDesk():bool
    {
        if (array_key_exists($this->choose_figure, $this->checkerDesk) and
            array_key_exists($this->set_step, $this->checkerDesk))
            return true;
    }

    public function checkStep()
    {

    }


}
