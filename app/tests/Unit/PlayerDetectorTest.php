<?php

use Src\Team\Black;
use Src\Team\PlayerDetector;
use Src\Team\PlayerInterface;
use Src\Team\White;

test('player detector', function () {
    $white = new White('Roman');
    $black = new Black('Vovan');
    $detector = new PlayerDetector($white, $black);
    expect($detector->detect('1'))->toBeInstanceOf(PlayerInterface::class)
        ->and($detector->detect('1')->getName())->toBe('Roman');
});
