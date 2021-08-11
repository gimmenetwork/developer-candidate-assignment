<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    
    public function getAccessListByRoles($roles)
    {

        return $this->createQueryBuilder('r')
        ->select('r.accessList')
            ->andWhere('r.role in (:roles)')
            ->setParameter('roles', $roles)
            ->getQuery()
            ->execute();

        // $entityManager = $this->getEntityManager();
        // $query=  $entityManager->createQuery('Select r.accessList from \App\Entity\Role r where r.role in (:roles)')
        //     ->setParameter('roles', $roles);
        //     ->getQuery()
        //     ->excecute()
        // ;
    }

    /*
    public function findOneBySomeField($value): ?Role
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
