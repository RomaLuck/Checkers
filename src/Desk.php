<?php

final class Desk
{
    private $checkerDesk = [];
    private $gorizontalSideDesk = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
    private $verticalSideDesk = [1, 2, 3, 4, 5, 6, 7, 8];

    private function checkerDesk(): array
    {
        $this->checkerDesk = [];
        for ($i = 1; $i <= 8; $i++) {
            $this->checkerDesk[]  = [0 => "a", 1 => $i];
            $this->checkerDesk[]  = [0 => "b", 1 => $i];
            $this->checkerDesk[]  = [0 => "c", 1 => $i];
            $this->checkerDesk[]  = [0 => "d", 1 => $i];
            $this->checkerDesk[]  = [0 => "e", 1 => $i];
            $this->checkerDesk[]  = [0 => "f", 1 => $i];
            $this->checkerDesk[]  = [0 => "g", 1 => $i];
            $this->checkerDesk[]  = [0 => "h", 1 => $i];
        }
        return $this->checkerDesk;
    }

    public function getCheckerDesk(): array
    {
        return $this->checkerDesk();
    }

    public function getgorizontalSideDesk(): array
    {
        return $this->gorizontalSideDesk;
    }

    public function getverticalSideDesk(): array
    {
        return $this->verticalSideDesk;
    }
}
$desk = new Desk();
var_dump($desk->getCheckerDesk());
