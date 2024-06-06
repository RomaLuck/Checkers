<?php

namespace Src\Repository;

use Doctrine\ORM\EntityRepository;
use Src\Entity\GameLaunch;

/**
 * @method GameLaunch|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameLaunch|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameLaunch[] findAll()
 * @method GameLaunch[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameLaunchRepository extends EntityRepository
{
}
