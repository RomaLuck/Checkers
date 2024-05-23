<?php

use Src\CheckerDesk;
use Src\Game;
use Src\Teams\Black;
use Src\Teams\TeamPlayer;
use Src\Teams\White;

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
    expect($player)->toBeInstanceOf(TeamPlayer::class);
});

test('init player(exception)', function () {
    $cell = $this->game->transform('a4');
    $player = $this->game->initPlayer($cell);
})->throws(RuntimeException::class, 'Can not find player on this cell');
