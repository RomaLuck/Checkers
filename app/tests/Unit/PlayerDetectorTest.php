<?php

use App\Service\Game\Team\Black;
use App\Service\Game\Team\PlayerDetector;
use App\Service\Game\Team\PlayerInterface;
use App\Service\Game\Team\White;

test('player detector', function () {
    $white = new White('Roman');
    $black = new Black('Vovan');
    $detector = new PlayerDetector($white, $black);
    expect($detector->detect('1'))->toBeInstanceOf(PlayerInterface::class)
        ->and($detector->detect('1')->getName())->toBe('Roman');
});
