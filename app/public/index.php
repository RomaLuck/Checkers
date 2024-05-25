<?php

session_start();

use Src\Helpers\Router;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';
require BASE_PATH . 'src/functions.php';

$router = new Router();
require BASE_PATH . 'routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);