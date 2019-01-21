<?php

namespace App\Repository;

use App\Entity\CollectionGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CollectionGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectionGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectionGroup[]    findAll()
 * @method CollectionGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CollectionGroup::class);
    }

    // /**
    //  * @return CollectionGroup[] Returns an array of CollectionGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CollectionGroup
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
