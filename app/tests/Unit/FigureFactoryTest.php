<?php

use Src\Figure\Checker;
use Src\Figure\FigureFactory;
use Src\Figure\King;

test('figure factory', function () {
    $factory = new FigureFactory(1);
    expect($factory->create())->toBeInstanceOf(Checker::class);
    $factory = new FigureFactory(4);
    expect($factory->create())->toBeInstanceOf(King::class);
});

test('figure factory(exception)', function () {
    $figure = (new FigureFactory(5))->create();
})->throws(RuntimeException::class, 'Figure is not selected');
