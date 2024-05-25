<?php

session_unset();
session_destroy();

use Symfony\Component\Finder\Finder;

$finder = new Finder();
$logs = $finder->files()->in(__DIR__ . '/../logs')->name('*.log');
foreach ($logs as $log) {
    unlink($log->getRealPath());
}

view('end_game.view.php');