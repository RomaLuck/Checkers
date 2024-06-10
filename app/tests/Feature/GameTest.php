<?php

use Src\Game\CheckerDesk;
use Src\Game\Game;
use Src\Game\Team\Black;
use Src\Game\Team\White;
use Src\Helpers\LogReader;

beforeEach(function () {
    $this->white = new White('Roma');
    $this->black = new Black('Olena');
    $this->game = new Game($this->white, $this->black);
    $_SESSION['desk'] = CheckerDesk::START_DESK;
});

afterEach(function () {
    $_SESSION['desk'] = null;
});

test('transform', function () {
    $result = $this->game->transformInputData('a2');

    expect($result)->tobe([0, 1]);
});

test('transform(unavailable exception)', function () {
    $this->game->transformInputData('a10');
})->throws(RuntimeException::class, 'Cell is unavailable');

test('transform(incorrect exception)', function () {
    $this->game->transformInputData('10');
})->throws(RuntimeException::class, 'Cell is incorrect');

test('run', function () {
    $this->game->run('a3', 'b4');

    $updatedDesk = $this->game->getDesk()->getDeskData();
    expect($updatedDesk[0][2])->toBe(0)
        ->and($updatedDesk[1][3])->toBe(1);
});

test('false direction', function () {
    $this->game->run('a3', 'b4');
    $this->game->run('b4', 'a3');

    $updatedDesk = $this->game->getDesk()->getDeskData();

    expect($updatedDesk[0][2])->toBe(0)
        ->and($updatedDesk[1][3])->toBe(1);

    $logContent = LogReader::getLastLogs(1);

    expect($logContent[0])->toContain('The direction is wrong');
});

test('false step opportunity', function () {
    $this->game->run('a3', 'c5');

    $updatedDesk = $this->game->getDesk()->getDeskData();
    expect($updatedDesk[0][2])->toBe(1)
        ->and($updatedDesk[2][4])->toBe(0);

    $logContent = LogReader::getLastLogs(1);

    expect($logContent[0])->toContain('You do not have ability to reach this cell');
});