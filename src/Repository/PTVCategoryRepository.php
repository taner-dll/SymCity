<?php

namespace App\Repository;

use App\Entity\PTVCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PTVCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PTVCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PTVCategory[]    findAll()
 * @method PTVCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PTVCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PTVCategory::class);
    }

    // /**
    //  * @return PTVCategory[] Returns an array of PTVCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PTVCategory
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
