<?php

namespace App\Repository;

use App\Entity\Siparis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Siparis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Siparis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Siparis[]    findAll()
 * @method Siparis[]    findBy(array $criteria, array $SiparisBy = null, $limit = null, $offset = null)
 */
class SiparisRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Siparis::class);
    }

    // /**
    //  * @return Siparis[] Returns an array of Siparis objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->SiparisBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Siparis
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
