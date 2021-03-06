<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function eventFilter($params){

        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.confirm = :confirm')
            ->setParameter('confirm',1);

        if (isset($params['name'])):
            $qb->andWhere($qb->expr()->like('e.name',':name'))
                ->setParameter('name','%'.$params['name'].'%');
        endif;

        //bölge
        if (isset($params['place'])):
            $qb->andWhere('e.place = :place')
                ->setParameter('place',$params['place']);
        endif;

        //bölge
        if (isset($params['sub_place'])):
            $qb->andWhere('e.sub_place = :sub_place')
                ->setParameter('sub_place',$params['sub_place']);
        endif;

        $qb->orderBy('e.start','desc');
        return $qb->getQuery()->execute();
    }
}
