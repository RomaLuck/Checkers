<?php

declare(strict_types=1);

use Src\Helpers\Router;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';
require BASE_PATH . 'src/functions.php';

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH . '.env');

$session = new Session;
$session->start();

$request = Request::createFromGlobals();
$request->setSession($session);
$routes = include BASE_PATH . 'routes.php';

$context = new RequestContext();
$matcher = new UrlMatcher($routes, $context);

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$framework = new Router($matcher, $controllerResolver, $argumentResolver);
$response = $framework->handle($request);

$response->send();
