<?php

use App\Service\Game\Figure\Checker;
use App\Service\Game\Figure\FigureFactory;
use App\Service\Game\Figure\King;

test('figure factory', function () {
    $factory = new FigureFactory(1);
    expect($factory->create())->toBeInstanceOf(Checker::class);
    $factory = new FigureFactory(4);
    expect($factory->create())->toBeInstanceOf(King::class);
});

test('figure factory(exception)', function () {
    $figure = (new FigureFactory(5))->create();
})->throws(RuntimeException::class, 'Figure is not selected');
