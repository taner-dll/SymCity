<?php

namespace App\Repository;

use App\Entity\PTVComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PTVComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PTVComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PTVComment[]    findAll()
 * @method PTVComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PTVCommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PTVComment::class);
    }

    // /**
    //  * @return PTVComment[] Returns an array of PTVComment objects
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
    public function findOneBySomeField($value): ?PTVComment
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
