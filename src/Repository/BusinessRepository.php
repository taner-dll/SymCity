<?php

namespace App\Repository;

use App\Entity\Business;
use App\Entity\BusinessCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use function Doctrine\ORM\QueryBuilder;

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
    public function businessCategoryList(): array
    {
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


    public function businessGuideFilter($params){

        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->where('b.confirm = :confirm')
            ->setParameter('confirm', 1);

        if (isset($params['name'])):
            $qb->andWhere($qb->expr()->like('b.name', ':name'))
                ->setParameter('name', '%' . $params['name'] . '%');
        endif;

        //bölge
        if (isset($params['place'])):
            $qb->andWhere('b.place = :place')
                ->setParameter('place', $params['place']);
        endif;

        //bölge
        if (isset($params['sub_place'])):
            $qb->andWhere('b.sub_place = :sub_place')
                ->setParameter('sub_place', $params['sub_place']);
        endif;

        //kategori
        if (isset($params['cat'])):
            $qb->andWhere('b.category = :cat')
                ->setParameter('cat', $params['cat']);
        endif;

        $qb->orderBy('b.id', 'desc');
        return $qb->getQuery()->execute();

    }


}
