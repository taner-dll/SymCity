<?php

namespace App\Repository;

use App\Entity\SiparisDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SiparisDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiparisDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiparisDetail[]    findAll()
 * @method SiparisDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiparisDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SiparisDetail::class);
    }

    // /**
    //  * @return OrderDetail[] Returns an array of OrderDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderDetail
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
