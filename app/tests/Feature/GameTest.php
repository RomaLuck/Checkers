<?php

use Src\CheckerDesk;
use Src\Game;
use Src\Helpers\LogReader;
use Src\Team\Black;
use Src\Team\TeamPlayerInterface;
use Src\Team\White;

beforeEach(function () {
    $this->white = new White('Roma');
    $this->black = new Black('Olena');
    $this->game = new Game($this->white, $this->black);
    $this->desk = CheckerDesk::initDesk();
});

test('transform', function () {
    $result = $this->game->transform('a2');

    expect($result)->tobe([0, 1]);
});

test('transform(unavailable exception)', function () {
    $this->game->transform('a10');
})->throws(RuntimeException::class, 'Cell is unavailable');

test('transform(incorrect exception)', function () {
    $this->game->transform('10');
})->throws(RuntimeException::class, 'Cell is incorrect');

test('init player', function () {
    $cell = $this->game->transform('a3');
    $player = $this->game->initPlayer($cell);

    expect($player)->toBeInstanceOf(TeamPlayerInterface::class);
});

test('init player(exception)', function () {
    $cell = $this->game->transform('a4');
    $this->game->initPlayer($cell);
})->throws(RuntimeException::class, 'Can not find player on this cell');

test('run', function () {
    $this->game->run('a3','b4');

    $updatedDesk = $this->game->getDesk();
    expect($updatedDesk[0][2])->toBe(0)
        ->and($updatedDesk[1][3])->toBe(1);
});

test('false direction', function () {
    $this->game->run('a3','b4');
    $this->game->run('b4','a3');

    $updatedDesk = $this->game->getDesk();
    expect($updatedDesk[0][2])->toBe(0)
        ->and($updatedDesk[1][3])->toBe(1);

    $logContent = LogReader::read(1);

    expect($logContent[0])->toContain('Something went wrong. Follow the rules!');
});
