<?php

namespace Src\Middleware;

enum AuthMiddlewareDetection: string
{
    case GUEST = 'guest';
    case AUTH = 'auth';

    public function detect(): AuthMiddlewareInterface
    {
        return match ($this) {
            self::GUEST => new GuestMiddleware(),
            self::AUTH => new AuthMiddleware()
        };
    }
}