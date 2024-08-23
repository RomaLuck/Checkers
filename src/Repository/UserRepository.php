<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null   find($id, $lockMode = null, $lockVersion = null)
 * @method User|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<User> findAll()
 * @method array<User> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByRole($role): ?UserInterface
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getComputerPLayer(): UserInterface
    {
        $computer = $this->findOneByRole('ROLE_COMPUTER');
        if (!$computer) {
            $computer = new User();
            $computer->setUsername('computer');
            $computer->setRoles(['ROLE_COMPUTER']);
            $computer->setPassword('********');

            $this->getEntityManager()->persist($computer);
            $this->getEntityManager()->flush();
        }

        return $computer;
    }
}
