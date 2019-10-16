<?php

namespace App\Repository;

use App\Entity\PlacesToVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlacesToVisit|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlacesToVisit|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlacesToVisit[]    findAll()
 * @method PlacesToVisit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlacesToVisitRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlacesToVisit::class);
    }

    // /**
    //  * @return PlacesToVisit[] Returns an array of PlacesToVisit objects
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
    public function findOneBySomeField($value): ?PlacesToVisit
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
