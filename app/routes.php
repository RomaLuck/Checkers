<?php

declare(strict_types=1);

use Src\Controller\GameController;
use Src\Controller\RegistrationController;
use Src\Controller\SecurityController;

$router->add('GET', '/', [GameController::class, 'index'])->middleware('auth');
$router->add('GET', '/registration', [RegistrationController::class, 'index'])->middleware('guest');
$router->add('POST', '/registration', [RegistrationController::class, 'register'])->middleware('guest');
$router->add('GET', '/login', [SecurityController::class, 'index'])->middleware('guest');
$router->add('POST', '/login', [SecurityController::class, 'login'])->middleware('guest');
$router->add('GET', '/game', [GameController::class, 'game'])->middleware('auth');
$router->add('POST', '/create', [GameController::class, 'create'])->middleware('auth');
$router->add('POST', '/join', [GameController::class, 'join'])->middleware('auth');
$router->add('GET', '/update', [GameController::class, 'update'])->middleware('auth');
$router->add('POST', '/update', [GameController::class, 'update'])->middleware('auth');
$router->add('GET', '/end', [GameController::class, 'end'])->middleware('auth');
$router->add('GET', '/logout', [SecurityController::class, 'logout'])->middleware('auth');