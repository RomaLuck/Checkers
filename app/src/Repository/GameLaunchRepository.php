<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\GameLaunch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GameLaunch|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameLaunch|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameLaunch[] findAll()
 * @method GameLaunch[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameLaunchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameLaunch::class);
    }
}
