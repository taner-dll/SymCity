<?php

namespace App\Repository;

use App\Entity\BusinessCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BusinessCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BusinessCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BusinessCategory[]    findAll()
 * @method BusinessCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BusinessCategory::class);
    }

    public function businessCategorySort(){
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.sort','asc')
            ->getQuery();
        return $qb->execute();
    }





}
