<?php

namespace App\Service\Cache;

use App\Entity\GameLaunch;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UserCacheService
{
    public function __construct(
        private CacheInterface $cache
    )
    {
    }

    public function getCachedWhiteTeamUser(GameLaunch $gameLaunch, string $roomId): array
    {
        return $this->cache->get('white_user_' . $roomId, function (ItemInterface $item) use ($gameLaunch): array {
            $item->expiresAfter(3600);

            $user = $gameLaunch->getWhiteTeamUser();
            if (!$user) {
                throw new \RuntimeException('User not found');
            }

            return [
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ];
        });
    }

    public function getCachedBlackTeamUser(GameLaunch $gameLaunch, string $roomId): array
    {
        return $this->cache->get('black_user_' . $roomId, function (ItemInterface $item) use ($gameLaunch): array {
            $item->expiresAfter(3600);

            $user = $gameLaunch->getBlackTeamUser();
            if (!$user) {
                throw new \RuntimeException('User not found');
            }

            return [
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ];
        });
    }
}
