<?php

declare(strict_types=1);

use Src\Controller\GameController;
use Src\Controller\RegistrationController;
use Src\Controller\SecurityController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$route = new Route('/', ['_controller' => [new GameController(), 'index']]);
$route->setMethods(['GET']);
$routes->add('games_list_view', $route);

$route = new Route('/registration', ['_controller' => [new RegistrationController(), 'index']]);
$route->setMethods(['GET']);
$routes->add('registration_view', $route);

$route = new Route('/registration', ['_controller' => [new RegistrationController(), 'register']]);
$route->setMethods(['POST']);
$routes->add('registration', $route);

$route = new Route('/login', ['_controller' => [new SecurityController(), 'index']]);
$route->setMethods(['GET']);
$routes->add('login_view', $route);

$route = new Route('/login', ['_controller' => [new SecurityController(), 'login']]);
$route->setMethods(['POST']);
$routes->add('login', $route);

$route = new Route('/game', ['_controller' => [new GameController(), 'game']]);
$route->setMethods(['GET']);
$routes->add('game_view', $route);

$route = new Route('/create', ['_controller' => [new GameController(), 'create']]);
$route->setMethods(['POST']);
$routes->add('game_create', $route);

$route = new Route('/join', ['_controller' => [new GameController(), 'join']]);
$route->setMethods(['POST']);
$routes->add('join_to_game', $route);

$route = new Route('/update', ['_controller' => [new GameController(), 'update']]);
$route->setMethods(['POST', 'GET']);
$routes->add('game_update', $route);

$route = new Route('/end', ['_controller' => [new GameController(), 'end']]);
$route->setMethods(['GET']);
$routes->add('game_end', $route);

$route = new Route('/logout', ['_controller' => [new SecurityController(), 'logout']]);
$route->setMethods(['GET']);
$routes->add('logout', $route);

return $routes;