<?php

use Src\Game\Team\Black;
use Src\Game\Team\PlayerDetector;
use Src\Game\Team\PlayerInterface;
use Src\Game\Team\White;

test('player detector', function () {
    $white = new White('Roman');
    $black = new Black('Vovan');
    $detector = new PlayerDetector($white, $black);
    expect($detector->detect('1'))->toBeInstanceOf(PlayerInterface::class)
        ->and($detector->detect('1')->getName())->toBe('Roman');
});
