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

    public function announceFilter($params)
    {

        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm', 1);

        if (isset($params['title'])):
            $qb->andWhere($qb->expr()->like('a.name', ':name'))
                ->setParameter('name', '%' . $params['title'] . '%');
        endif;

        //bölge
        if (isset($params['place'])):
            $qb->andWhere('a.place = :place')
                ->setParameter('place', $params['place']);
        endif;

        //bölge
        if (isset($params['sub_place'])):
            $qb->andWhere('a.sub_place = :sub_place')
                ->setParameter('sub_place', $params['sub_place']);
        endif;

        //kategori
        if (isset($params['cat'])):
            $qb->andWhere('a.category = :cat')
                ->setParameter('cat', $params['cat']);
        endif;

        $qb->orderBy('a.id', 'desc');
        return $qb->getQuery()->execute();
    }
}
