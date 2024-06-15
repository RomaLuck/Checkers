<?php

namespace Src\Repository;

use Doctrine\ORM\EntityRepository;
use Src\Entity\Log;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[] findAll()
 * @method Log[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends EntityRepository
{
}
