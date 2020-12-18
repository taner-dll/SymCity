<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function articleFilter(Request $request)
    {

        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm', 1);

        if ($request->query->get('title')):
            $qb->andWhere($qb->expr()->like('a.title', ':title'))
                ->setParameter('title', '%' . $request->query->get('title') . '%');
        endif;

        return $qb->getQuery()->execute();
    }


}
