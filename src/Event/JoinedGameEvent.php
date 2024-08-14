<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\GameLaunch;
use Symfony\Contracts\EventDispatcher\Event;

final class JoinedGameEvent extends Event
{
    public function __construct(private GameLaunch $gameLaunch, private string $username)
    {
    }

    public function getGameLaunch(): GameLaunch
    {
        return $this->gameLaunch;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
