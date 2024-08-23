<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\GameLaunch;
use Symfony\Contracts\EventDispatcher\Event;

final class GameStatusUpdatedEvent extends Event
{
    public function __construct(private GameLaunch $gameLaunch) {}

    public function getGameLaunch(): GameLaunch
    {
        return $this->gameLaunch;
    }

    public function setGameLaunch(GameLaunch $gameLaunch): void
    {
        $this->gameLaunch = $gameLaunch;
    }
}
