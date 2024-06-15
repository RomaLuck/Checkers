<?php

namespace Src\Middleware;

use Symfony\Component\HttpFoundation\Request;

interface AuthMiddlewareInterface
{
    public function handle(Request $request);
}