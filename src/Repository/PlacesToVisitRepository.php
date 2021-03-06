<?php

namespace App\Repository;

use App\Entity\AdSubCategory;
use App\Entity\PlacesToVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

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
    
    public function ptvFilter($params){


        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.id','desc');

        if (isset($params['type'])):
            $qb->andWhere('p.type = :type')
                ->setParameter('type',$params['type']);
        endif;

        if (isset($params['name'])):
            $qb->andWhere($qb->expr()->like('p.name',':name'))
                ->setParameter('name','%'.$params['name'].'%');
        endif;

        //bölge
        if (isset($params['place'])):
            $qb->andWhere('p.place = :place')
                ->setParameter('place',$params['place']);
        endif;

        //bölge
        if (isset($params['sub_place'])):
            $qb->andWhere('p.sub_place = :sub_place')
                ->setParameter('sub_place',$params['sub_place']);
        endif;


        return $qb->getQuery()->execute();
    }

}
