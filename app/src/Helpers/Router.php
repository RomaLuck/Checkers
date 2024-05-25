<?php

namespace Src\Helpers;

class Router
{
    protected array $routes = [];

    public function add($method, $uri, $controller)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
        ];

        return $this;
    }

    public function get($uri, $controller)
    {
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        return $this->add('POST', $uri, $controller);
    }


    public function route($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {

                return require base_path('controllers/' . $route['controller']);
            }
        }

        return $this->abort();
    }

    protected function abort($code = 404)
    {
        http_response_code($code);

        return require base_path("views/{$code}.php");
    }
}
