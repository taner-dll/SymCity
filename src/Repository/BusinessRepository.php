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


    public function businessGuideFilter(Request $request){

        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->leftJoin('b.category', 'c')
            ->leftJoin('b.place', 'p')
            ->leftJoin('p.parent','pr')
            ->where('b.confirm = :confirm')
            ->setParameter('confirm',1);

        if ($request->query->get('name')):
            $qb->andWhere($qb->expr()->like('b.name',':bname'))
                ->setParameter('bname','%'.$request->query->get('name').'%');
        endif;

        if ($request->query->get('cat')):
            $qb->andWhere('c.short_name = :sname')
                ->setParameter('sname',$request->query->get('cat'));
        endif;

        if ($request->query->get('place')):
            $qb->andWhere('pr.id = :pr_id')
                ->setParameter('pr_id',$request->query->get('place'));
        endif;

        if ($request->query->get('sub_place')):
            $qb->andWhere('b.place = :pl_id')
                ->setParameter('pl_id',$request->query->get('sub_place'));
        endif;



        return $qb->getQuery()->execute();
    }


}
