<?php

namespace Src\Repository;

use Doctrine\ORM\EntityRepository;
use Src\Entity\Game;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[] findAll()
 * @method Game[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends EntityRepository
{
}
