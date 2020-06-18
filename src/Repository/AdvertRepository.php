<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    /**
     * Adverts page - right colum, category list.
     * @return Advert[]
     */
    public function advertCategoryList(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id as id, count(c.id) as total, c.short_name as shortname')
            ->innerJoin('a.category', 'c')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm',1)
            ->groupBy('shortname')
            ->orderBy('c.sort','asc')
            ->getQuery();
        return $qb->execute();
    }

    /**
     * Adverts page - right colum, sub category list.
     * @return Advert[]
     */
    public function advertSubCategoryList(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id as id, count(sc.id) as total, sc.short_name as shortname')
            ->innerJoin('a.sub_category', 'sc')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm',1)
            ->groupBy('shortname')
            ->orderBy('sc.sort','asc')
            ->getQuery();
        return $qb->execute();
    }

    public function advertFilter(Request $request){

        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.category', 'c')
            ->leftJoin('a.place', 'p')
            ->leftJoin('a.sub_category', 'sc')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm',1);

        if ($request->query->get('name')):
            $qb->andWhere($qb->expr()->like('a.title',':bname'))
                ->setParameter('bname','%'.$request->query->get('name').'%');
        endif;

        if ($request->query->get('cat')):
            $qb->andWhere('c.short_name = :sname')
                ->setParameter('sname',$request->query->get('cat'));
        endif;

        if ($request->query->get('sub')):
            $qb->andWhere('sc.short_name = :scname')
                ->setParameter('scname',$request->query->get('sub'));
        endif;

        if ($request->query->get('place')):
            $qb->andWhere('a.place = :pl_id')
                ->setParameter('pl_id',$request->query->get('place'));
        endif;

        return $qb->getQuery()->execute();
    }
}
