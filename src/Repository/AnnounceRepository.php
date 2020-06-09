<?php

namespace App\Repository;

use App\Entity\Announce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Announce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Announce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Announce[]    findAll()
 * @method Announce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnounceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Announce::class);
    }

    public function announceFilter(Request $request){

        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.place', 'p')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm',1);

        if ($request->query->get('name')):
            $qb->andWhere($qb->expr()->like('a.name',':bname'))
                ->setParameter('bname','%'.$request->query->get('name').'%');
        endif;



        if ($request->query->get('place')):
            $qb->andWhere('a.place = :pl_id')
                ->setParameter('pl_id',$request->query->get('place'));
        endif;

        return $qb->getQuery()->execute();
    }
}
