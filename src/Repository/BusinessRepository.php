<?php

namespace App\Repository;

use App\Entity\Business;
use App\Entity\BusinessCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Business|null find($id, $lockMode = null, $lockVersion = null)
 * @method Business|null findOneBy(array $criteria, array $orderBy = null)
 * @method Business[]    findAll()
 * @method Business[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Business::class);
    }


    /**
     * Business guide - right colum, category list.
     * @return Business[]
     */
    public function businessCategoryList(){
        $qb = $this->createQueryBuilder('b')
            ->select('b.id as id, count(c.id) as total, c.short_name as shortname')
            ->innerJoin('b.category', 'c')
            ->where('b.confirm = :confirm')
            ->setParameter('confirm',1)
            ->groupBy('shortname')
            ->orderBy('c.sort','asc')
            ->getQuery();
        return $qb->execute();
    }


    public function businessGuideFilter($options){



        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->where('b.confirm = :confirm')
            ->setParameter('confirm',1)
            ->getQuery();
        return $qb->execute();
    }


}
