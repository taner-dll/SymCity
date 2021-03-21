<?php

namespace App\Repository;

use App\Entity\ArticleClaps;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ArticleClaps|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleClaps|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleClaps[]    findAll()
 * @method ArticleClaps[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleClapsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleClaps::class);
    }

    // /**
    //  * @return ArticleClaps[] Returns an array of ArticleClaps objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArticleClaps
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
