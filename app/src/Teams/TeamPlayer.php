<?php

namespace Src\Teams;

interface TeamPlayer
{
    public function getName();

    public function getTeamNumber(): int;
}