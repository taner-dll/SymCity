<?php

namespace App\Repository;

use App\Entity\AdCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AdCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdCategory[]    findAll()
 * @method AdCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AdCategory::class);
    }

    /**
     * Business guide - right colum, category list.
     * @return AdCategory[]
     */
    public function adCategorySort(){
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.active = :active')
            ->setParameter('active',1)
            ->orderBy('a.sort','asc')
            ->getQuery();
        return $qb->execute();
    }
}
