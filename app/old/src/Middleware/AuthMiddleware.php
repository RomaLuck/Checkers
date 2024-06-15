<?php

namespace Src\Middleware;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthMiddleware implements AuthMiddlewareInterface
{
    public function handle(Request $request): ?Response
    {
        $session = $request->getSession();
        if (!$session->has('user')) {
            return new RedirectResponse('/login');
        }
        return null;
    }
}