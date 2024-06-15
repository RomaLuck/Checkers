<?php

declare(strict_types=1);

use Src\Helpers\Router;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';
require BASE_PATH . 'src/functions.php';

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH . '.env');

$session = new Session;
$session->start();

$request = Request::createFromGlobals();
$request->setSession($session);

$router = new Router();

require_once BASE_PATH . "routes.php";

$router->dispatch($request)->send();
