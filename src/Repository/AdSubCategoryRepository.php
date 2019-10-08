<?php

namespace App\Repository;

use App\Entity\AdSubCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AdSubCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdSubCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdSubCategory[]    findAll()
 * @method AdSubCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdSubCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AdSubCategory::class);
    }

    // /**
    //  * @return AdSubCategory[] Returns an array of AdSubCategory objects
    //  */

    /*
    public function subCategoryNames(){
        $qb = $this->createQueryBuilder('c')
            ->select('a.short_name')
            ->innerJoin('c.adCategory','a')
            ->groupBy('c.adCategory')
            ->getQuery();
        return $qb->execute();
    }

    /*
    public function findOneBySomeField($value): ?AdSubCategory
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
