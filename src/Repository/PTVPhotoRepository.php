<?php

namespace App\Repository;

use App\Entity\PTVPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PTVPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method PTVPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method PTVPhoto[]    findAll()
 * @method PTVPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PTVPhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PTVPhoto::class);
    }

    // /**
    //  * @return PTVPhoto[] Returns an array of PTVPhoto objects
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
    public function findOneBySomeField($value): ?PTVPhoto
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
