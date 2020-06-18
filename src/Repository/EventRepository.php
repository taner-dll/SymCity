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

    public function eventFilter(Request $request){

        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->leftJoin('e.place', 'p')
            ->where('e.confirm = :confirm')
            ->setParameter('confirm',1);

        if ($request->query->get('name')):
            $qb->andWhere($qb->expr()->like('e.name',':bname'))
                ->setParameter('bname','%'.$request->query->get('name').'%');
        endif;



        if ($request->query->get('place')):
            $qb->andWhere('e.place = :pl_id')
                ->setParameter('pl_id',$request->query->get('place'));
        endif;

        return $qb->getQuery()->execute();
    }
}
