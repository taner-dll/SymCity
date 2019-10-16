<?php

namespace App\Repository;

use App\Entity\MunicipalityNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MunicipalityNews|null find($id, $lockMode = null, $lockVersion = null)
 * @method MunicipalityNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method MunicipalityNews[]    findAll()
 * @method MunicipalityNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MunicipalityNewsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MunicipalityNews::class);
    }

    // /**
    //  * @return MunicipalityNews[] Returns an array of MunicipalityNews objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MunicipalityNews
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
