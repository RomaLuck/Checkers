<?php

session_unset();
session_destroy();

use Src\Helpers\LogReader;

LogReader::deleteLogFiles();

view('end_game.view.php');